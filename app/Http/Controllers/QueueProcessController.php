<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class QueueProcessController extends Controller
{
    public function process()
    {
        // Validate the secret key (set this in your .env file)
        // if ($secret !== config('app.queue_secret')) {
        //     abort(404);
        // }

        // Run the campaign dispatch command
        Artisan::call('campaign:dispatch', ['--batch' => 50]);
        
        // Also run the queue worker for a limited time
        Artisan::call('queue:work', [
            '--once' => true,
            '--tries' => 3,
            '--timeout' => 120
        ]);

        return response()->json(['status' => 'success', 'message' => 'Queue processed']);
    }
}