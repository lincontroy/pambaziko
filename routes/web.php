<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\QueueProcessController;
use App\Http\Controllers\QueueMonitorController;
use App\Http\Controllers\PaybillController;

// Authentication routes
Auth::routes();

Route::get('/test-paybill-model', function() {
    $paybill = \App\Models\Paybill::first();
    
    if (!$paybill) {
        return "No paybill found";
    }
    
    // Test direct attribute access
    return response()->json([
        'consumer_key' => $paybill->consumer_key,
        'consumer_secret' => $paybill->consumer_secret,
        'passkey' => $paybill->passkey,
        'direct_access_works' => true
    ]);
});

Route::get('/test-safaricom-api', function() {
    $paybill = \App\Models\Paybill::first();
    
    if (!$paybill) {
        return "No paybill found";
    }
    
    // Test access token request
    $client = new \GuzzleHttp\Client(['verify' => false]);
    $auth = base64_encode($paybill->consumer_key . ':' . $paybill->consumer_secret);
    
    try {
        $response = $client->get('https://api.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials', [
            'headers' => [
                'Authorization' => 'Basic ' . $auth,
            ],
            'timeout' => 15,
        ]);
        
        $data = json_decode($response->getBody()->getContents(), true);
        
        return response()->json([
            'success' => true,
            'access_token' => isset($data['access_token']) ? substr($data['access_token'], 0, 50) . '...' : 'Not found',
            'full_response' => $data
        ]);
        
    } catch (\GuzzleHttp\Exception\RequestException $e) {
        $errorResponse = $e->hasResponse() ? $e->getResponse()->getBody()->getContents() : 'No response';
        
        return response()->json([
            'success' => false,
            'error' => $e->getMessage(),
            'response' => $errorResponse,
            'status_code' => $e->getResponse() ? $e->getResponse()->getStatusCode() : 'Unknown',
            'credentials_used' => [
                'consumer_key' => $paybill->consumer_key,
                'consumer_secret' => $paybill->consumer_secret,
                'auth_header' => 'Basic ' . $auth
            ]
        ]);
    }
});
Route::post('/stk-callback', function(Request $request) {
    \Log::info('STK Callback Received', $request->all());
    return response()->json(['ResultCode' => 0, 'ResultDesc' => 'Success']);
})->name('stk.callback');

// Queue processing route (to be called by cron)
Route::get('/process-queue', [QueueProcessController::class, 'process'])->name('queue.process');

// Protected routes
Route::middleware(['auth'])->group(function () {
    // Dashboard
Route::get('/dashboard', [App\Http\Controllers\HomeController::class, 'index'])->name('dashboard');

    // Paybill routes
    Route::resource('paybills', App\Http\Controllers\PaybillController::class);
    
    // Upload routes
    Route::resource('uploads', App\Http\Controllers\UploadController::class);
    Route::get('uploads/{upload}/contacts', [App\Http\Controllers\UploadController::class, 'contacts'])->name('uploads.contacts');
    
    // Campaign routes
    Route::resource('campaigns', App\Http\Controllers\CampaignController::class);
    Route::post('campaigns/{campaign}/start', [App\Http\Controllers\CampaignController::class, 'start'])->name('campaigns.start');
    Route::post('campaigns/{campaign}/pause', [App\Http\Controllers\CampaignController::class, 'pause'])->name('campaigns.pause');
    Route::post('campaigns/{campaign}/retry', [App\Http\Controllers\CampaignController::class, 'retry'])->name('campaigns.retry');
    Route::get('campaigns/{campaign}/details', [App\Http\Controllers\CampaignController::class, 'details'])->name('campaigns.details');
    
    // Queue monitor
    Route::get('queue/monitor', [App\Http\Controllers\QueueMonitorController::class, 'index'])->name('queue.monitor');
    Route::post('queue/retry/{id}', [App\Http\Controllers\QueueMonitorController::class, 'retry'])->name('queue.retry');
    Route::delete('queue/delete/{id}', [App\Http\Controllers\QueueMonitorController::class, 'delete'])->name('queue.delete');
});

// Home route
Route::get('/', function () {
    return redirect()->route('dashboard');
});