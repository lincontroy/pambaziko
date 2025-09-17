<?php

namespace App\Jobs;

use App\Models\Contact;
use App\Models\Paybill;
use App\Models\Campaign;
use App\Services\SafaricomSTKService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessSTKPushJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 120;

    protected $contact;
    protected $paybill;
    protected $campaign;

    public function __construct(Contact $contact, Paybill $paybill, Campaign $campaign)
    {
        $this->contact = $contact;
        $this->paybill = $paybill;
        $this->campaign = $campaign;
    }

    public function handle(SafaricomSTKService $stkService)
    {
        $contactId = $this->contact->id;
        $paybillId = $this->paybill->id;
        $campaignId = $this->campaign->id;

        // dd($campaignId);

        Log::info("Processing STK Push Job", [
            'contact_id' => $contactId,
            'paybill_id' => $paybillId,
            'campaign_id' => $campaignId,
            'phone' => $this->contact->phone,
            'amount' => $this->contact->amount
        ]);

        // Check if paybill still has quota
        $paybill = Paybill::find($paybillId);
        
        if (!$paybill->hasQuota()) {
            Log::warning("Paybill quota exceeded", [
                'paybill_id' => $paybillId,
                'current_count' => $paybill->current_count,
                'daily_limit' => $paybill->daily_limit,
                'contact_id' => $contactId
            ]);
            
            $this->release(3600); // Retry after 1 hour
            return;
        }

        try {
            // Normalize phone number
            $phone = $this->normalizePhone($this->contact->phone);
            
            Log::debug("Normalized phone number", [
                'original' => $this->contact->phone,
                'normalized' => $phone,
                'contact_id' => $contactId
            ]);

            // Send STK push
            Log::info("Sending STK Push request", [
                'contact_id' => $contactId,
                'paybill_id' => $paybillId,
                'phone' => $phone,
                'amount' => $this->contact->amount
            ]);


            $amount = $this->formatAmount($this->contact->amount);
            $response = $stkService->sendSTKPush(
                $paybill,
                $phone,
                $amount,
                "Payment for {$this->contact->amount}"
            );

            Log::info("STK Push response received", [
                'contact_id' => $contactId,
                'response' => $response,
                'paybill_id' => $paybillId
            ]);

            // Update contact status
            $this->contact->update([
                'status' => 'sent',
                'response_json' => $response,
                'attempts' => $this->contact->attempts + 1,
                'last_attempt_at' => now(),
            ]);

            // Increment paybill count
            $paybill->incrementCount();

            // Update campaign stats
            $this->campaign->increment('sent_count');

            Log::info("STK Push processed successfully", [
                'contact_id' => $contactId,
                'paybill_id' => $paybillId,
                'campaign_id' => $campaignId,
                'new_paybill_count' => $paybill->current_count
            ]);

        } catch (\Exception $e) {
            Log::error("STK Push failed", [
                'contact_id' => $contactId,
                'paybill_id' => $paybillId,
                'campaign_id' => $campaignId,
                'phone' => $this->contact->phone,
                'amount' => $this->contact->amount,
                'error_message' => $e->getMessage(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'error_trace' => $e->getTraceAsString()
            ]);

            $this->contact->update([
                'status' => 'failed',
                'response_json' => [
                    'error' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'timestamp' => now()->toISOString()
                ],
                'attempts' => $this->contact->attempts + 1,
                'last_attempt_at' => now(),
            ]);

            $this->campaign->increment('failed_count');
            
            // Re-throw to trigger failed method
            throw $e;
        }
    }

    private function normalizePhone($phone)
    {
        $originalPhone = $phone;
        
        // Remove all non-digit characters
        $phone = preg_replace('/\D/', '', $phone);
        
        // If empty after cleaning, return as is
        if (empty($phone)) {
            Log::warning("Phone number is empty after cleaning", [
                'original_phone' => $originalPhone,
                'contact_id' => $this->contact->id
            ]);
            return $phone;
        }
    
        // Handle 9-digit numbers (like 704800563)
        if (strlen($phone) === 9) {
            // Check if it starts with 7 ( Kenyan mobile numbers)
            if (substr($phone, 0, 1) === '7') {
                return '254' . $phone;
            }
            // If it starts with other digits, still convert but log warning
            Log::warning("9-digit number doesn't start with 7", [
                'original_phone' => $originalPhone,
                'normalized_phone' => '254' . $phone,
                'contact_id' => $this->contact->id
            ]);
            return '254' . $phone;
        }
        
        // Handle 10-digit numbers (like 0704800563)
        if (strlen($phone) === 10 && substr($phone, 0, 1) === '0') {
            return '254' . substr($phone, 1);
        }
        
        // Handle 12-digit numbers (like 254704800563)
        if (strlen($phone) === 12 && substr($phone, 0, 3) === '254') {
            return $phone;
        }
        
        // Handle 13-digit numbers with + (like +254704800563)
        if (strlen($phone) === 13 && substr($phone, 0, 4) === '+254') {
            return substr($phone, 1); // Remove the +
        }
        
        // Handle other international formats
        if (strlen($phone) > 12) {
            // If it's already an international number, return as is
            return $phone;
        }
    
        // For any other format, try to make it 254 format
        if (strlen($phone) < 9) {
            // Number is too short
            Log::warning("Phone number is too short", [
                'original_phone' => $originalPhone,
                'normalized_phone' => $phone,
                'contact_id' => $this->contact->id
            ]);
            return $phone;
        }
        
        // Default: assume it's a 9-digit number without leading 0
        if (strlen($phone) === 9) {
            return '254' . $phone;
        }
        
        // If number is 10 digits but doesn't start with 0, remove first digit and add 254
        if (strlen($phone) === 10 && substr($phone, 0, 1) !== '0') {
            return '254' . substr($phone, 1);
        }
    
        // Log invalid phone format but still return the normalized version
        Log::warning("Phone number format may be invalid", [
            'original_phone' => $originalPhone,
            'normalized_phone' => $phone,
            'contact_id' => $this->contact->id
        ]);
        
        return $phone;
    }
    private function formatAmount($amount)
{
    // Convert to float first to handle any string formatting
    $amount = floatval($amount);
    
    // Safaricom requires whole numbers (no decimals) for STK push
    // Round to nearest whole number
    $amount = round($amount);
    
    // Ensure amount is at least 1 (Safaricom minimum)
    $amount = max(1, $amount);
    
    // Ensure amount doesn't exceed Safaricom maximum (typically 150,000 for M-Pesa)
    $amount = min(150000, $amount);
    
    return $amount;
}
    public function failed(\Exception $exception)
    {
        $contactId = $this->contact->id;
        $paybillId = $this->paybill->id;
        $campaignId = $this->campaign->id;

        Log::critical("STK Push Job failed completely after all attempts", [
            'contact_id' => $contactId,
            'paybill_id' => $paybillId,
            'campaign_id' => $campaignId,
            'phone' => $this->contact->phone,
            'amount' => $this->contact->amount,
            'attempts' => $this->contact->attempts,
            'final_error_message' => $exception->getMessage(),
            'final_error_file' => $exception->getFile(),
            'final_error_line' => $exception->getLine(),
            'final_error_trace' => $exception->getTraceAsString(),
            'failed_at' => now()->toISOString()
        ]);

        // Update contact status to failed
        $this->contact->update([
            'status' => 'failed',
            'response_json' => [
                'error' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'timestamp' => now()->toISOString(),
                'final_attempt' => true
            ],
            'attempts' => $this->contact->attempts + 1,
            'last_attempt_at' => now(),
        ]);

        // Update campaign stats
        $this->campaign->increment('failed_count');

        Log::info("Contact marked as failed after all attempts", [
            'contact_id' => $contactId,
            'campaign_id' => $campaignId,
            'total_attempts' => $this->contact->attempts
        ]);
    }
}