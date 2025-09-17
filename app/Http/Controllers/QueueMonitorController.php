<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;

class QueueMonitorController extends Controller
{
    /**
     * Display the queue monitoring dashboard.
     */
    public function index()
    {
        // Get pending jobs
        $pendingJobs = DB::table('jobs')
            ->orderBy('available_at', 'asc')
            ->get();

        // Get failed jobs
        $failedJobs = DB::table('failed_jobs')
            ->orderBy('failed_at', 'desc')
            ->get();

        return view('queue.monitor', compact('pendingJobs', 'failedJobs'));
    }

    /**
     * Retry a failed job.
     */
    public function retry($id)
    {
        try {
            Artisan::call('queue:retry', ['id' => $id]);
            return redirect()->back()->with('success', 'Job retried successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to retry job: ' . $e->getMessage());
        }
    }

    /**
     * Delete a job from the queue.
     */
    public function delete($id)
    {
        try {
            DB::table('jobs')->where('id', $id)->delete();
            return redirect()->back()->with('success', 'Job deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to delete job: ' . $e->getMessage());
        }
    }

    /**
     * Delete a failed job.
     */
    public function deleteFailed($id)
    {
        try {
            DB::table('failed_jobs')->where('id', $id)->delete();
            return redirect()->back()->with('success', 'Failed job deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to delete failed job: ' . $e->getMessage());
        }
    }
}