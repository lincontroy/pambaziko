<?php

namespace App\Console\Commands;

use App\Models\Campaign;
use App\Models\Contact;
use App\Jobs\ProcessSTKPushJob;
use Illuminate\Console\Command;

class CampaignDispatchCommand extends Command
{
    protected $signature = 'campaign:dispatch {--batch=100}';
    protected $description = 'Dispatch STK push jobs for active campaigns';

    public function handle()
    {
        $batchSize = $this->option('batch');
        
        Campaign::where('status', 'processing')
            ->with(['paybill', 'upload.contacts' => function($query) {
                $query->whereIn('status', ['pending', 'failed'])
                      ->where('attempts', '<', 3);
            }])
            ->chunk(10, function($campaigns) use ($batchSize) {
                foreach ($campaigns as $campaign) {
                    $this->dispatchJobsForCampaign($campaign, $batchSize);
                }
            });
    }

    private function dispatchJobsForCampaign(Campaign $campaign, $batchSize)
    {
        $contacts = $campaign->upload->contacts()
            ->whereIn('status', ['pending', 'failed'])
            ->where('attempts', '<', 3)
            ->limit($batchSize)
            ->get();

        foreach ($contacts as $contact) {
            if ($campaign->paybill->hasQuota()) {
                ProcessSTKPushJob::dispatch($contact, $campaign->paybill, $campaign);
                $contact->update(['status' => 'queued']);
            } else {
                // No quota left, pause the campaign
                $campaign->update(['status' => 'paused']);
                break;
            }
        }
    }
}