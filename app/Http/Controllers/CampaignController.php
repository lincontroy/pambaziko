<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\Upload;
use App\Models\Paybill;
use App\Models\Contact;
use Illuminate\Http\Request;
use App\Jobs\ProcessSTKPushJob;

class CampaignController extends Controller
{
    public function index()
    {
        $campaigns = Campaign::where('user_id', auth()->id())->with('paybill', 'upload')->latest()->get();
        return view('campaigns.index', compact('campaigns'));
    }

    public function create()
    {
        $uploads = Upload::where('user_id', auth()->id())->where('status', 'parsed')->get();
        $paybills = Paybill::where('user_id', auth()->id())->get();
        
        return view('campaigns.create', compact('uploads', 'paybills'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'upload_id' => 'required|exists:uploads,id',
            'paybill_id' => 'required|exists:paybills,id',
        ]);

        $upload = Upload::findOrFail($request->upload_id);
        $this->authorize('view', $upload);

        $campaign = Campaign::create([
            'user_id' => auth()->id(),
            'upload_id' => $request->upload_id,
            'paybill_id' => $request->paybill_id,
            'name' => $request->name,
            'total_count' => $upload->rows_count,
            'status' => 'pending',
        ]);

        return redirect()->route('campaigns.show', $campaign)->with('success', 'Campaign created successfully.');
    }

    public function show(Campaign $campaign)
    {
        $this->authorize('view', $campaign);
        
        $contacts = $campaign->upload->contacts()->paginate(50);
        return view('campaigns.show', compact('campaign', 'contacts'));
    }

    public function start(Campaign $campaign)
    {
        $this->authorize('update', $campaign);
        
        $campaign->update([
            'status' => 'processing',
            'started_at' => now(),
        ]);

        return back()->with('success', 'Campaign started.');
    }

    public function pause(Campaign $campaign)
    {
        $this->authorize('update', $campaign);
        
        $campaign->update(['status' => 'paused']);
        return back()->with('success', 'Campaign paused.');
    }

    public function retry(Campaign $campaign)
    {
        // $this->authorize('update', $campaign);
        
        // Get failed contacts for THIS SPECIFIC CAMPAIGN only
        $failedContacts = $campaign->upload->contacts()
            ->where('status', 'failed')
            ->where('attempts', '<', 993) // Only retry contacts with less than 3 attempts
            ->get();
    
        $retryCount = 0;
        $dispatchResults = [];
    
        foreach ($failedContacts as $contact) {
            try {
                // Reset contact status to pending for retry
                $contact->update([
                    'status' => 'pending',
                    'response_json' => null
                ]);
    
                // Dispatch new job for this contact
                ProcessSTKPushJob::dispatch($contact, $campaign->paybill, $campaign)
                ->delay(now()->addMinutes(2));
                
                $retryCount++;
                $dispatchResults[] = [
                    'contact_id' => $contact->id,
                    'phone' => $contact->phone,
                    'status' => 'dispatched'
                ];
    
            } catch (\Exception $e) {
                $dispatchResults[] = [
                    'contact_id' => $contact->id,
                    'phone' => $contact->phone,
                    'status' => 'failed',
                    'error' => $e->getMessage()
                ];
            }
        }
    
        // Update campaign failed count (subtract the retried contacts)
        $newFailedCount = max(0, $campaign->failed_count - $retryCount);
        $campaign->update([
            'failed_count' => $newFailedCount,
            'status' => 'processing' // Restart the campaign
        ]);
    
        // Store results in session for display
        session()->flash('retry_results', $dispatchResults);
    
        return back()->with('success', "Retrying {$retryCount} failed contacts.");
    }
}