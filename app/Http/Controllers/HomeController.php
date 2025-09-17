<?php

namespace App\Http\Controllers;
use App\Models\Campaign;
use App\Models\Paybill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // Total campaigns for this user
        $totalCampaigns = Campaign::where('user_id', auth()->id())->count();

        // Sent and failed counts
        $totalSent   = Campaign::where('user_id', auth()->id())
                        ->sum('sent_count');

        $totalFailed = Campaign::where('user_id', auth()->id())
                        ->sum('failed_count');

        // Quota left (assume daily quota = 1000 per user)
        $dailyQuota   = 1000;
        $usedToday    = Campaign::where('user_id', auth()->id())
                          ->whereDate('created_at', today())
                          ->sum('sent_count');
        $quotaLeft    = max(0, $dailyQuota - $usedToday);

        // Recent campaigns
        $recentCampaigns = Campaign::where('user_id', auth()->id())
                            ->latest()
                            ->take(5)
                            ->get()
                            ->map(function ($c) {
                                $c->progress_percentage = $c->sent_count + $c->failed_count > 0
                                    ? round(($c->sent_count / ($c->sent_count + $c->failed_count)) * 100)
                                    : 0;

                                $c->status_color = match ($c->status) {
                                    'completed' => 'success',
                                    'failed'    => 'danger',
                                    'running'   => 'info',
                                    default     => 'secondary',
                                };
                                return $c;
                            });

        // Queue jobs
        $pendingJobs = DB::table('jobs')->count();
        $failedJobs  = DB::table('failed_jobs')->count();

        // Paybill status (assume multiple paybills belong to this user)
        $paybills = Paybill::where('user_id', auth()->id())->get();

        return view('dashboard', compact(
            'totalCampaigns',
            'totalSent',
            'totalFailed',
            'quotaLeft',
            'recentCampaigns',
            'pendingJobs',
            'failedJobs',
            'paybills'
        ));
    }
}
