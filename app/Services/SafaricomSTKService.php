<?php

namespace App\Services;

use App\Models\Paybill;
use Illuminate\Support\Facades\Log;

class SafaricomSTKService
{
    protected $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('app.env') === 'production' 
            ? 'https://api.safaricom.co.ke' 
            : 'https://sandbox.safaricom.co.ke';
    }

    /**
     * Send STK Push request using cURL
     */
    public function sendSTKPush(Paybill $paybill, $phone, $amount, $description)
    {
        try {
            $consumerKey = $paybill->consumer_key;
            $consumerSecret = $paybill->consumer_secret;
            $businessShortCode = $paybill->paybill_number;
            $passkey = $paybill->passkey;

            // Generate timestamp and password
            $timestamp = date('YmdHis');
            $password = base64_encode($businessShortCode . $passkey . $timestamp);

            // Get access token
            $accessToken = $this->getAccessToken($consumerKey, $consumerSecret);

            Log::info('Sending STK Push', [
                'paybill' => $businessShortCode,
                'phone' => $phone,
                'amount' => $amount,
                'timestamp' => $timestamp
            ]);

            // Prepare STK Push request
            $stkHeader = [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $accessToken
            ];

            $curlPostData = [
                'BusinessShortCode' => $businessShortCode,
                'Password' => $password,
                'Timestamp' => $timestamp,
                'TransactionType' => 'CustomerPayBillOnline',
                'Amount' => intval($amount),
                'PartyA' => $phone,
                'PartyB' => $businessShortCode,
                'PhoneNumber' => $phone,
                'CallBackURL' => 'https://www.truecaller.com/',
                'AccountReference' => substr($description, 0, 12),
                'TransactionDesc' => $description
            ];

            $dataString = json_encode($curlPostData);

            // Initialize cURL for STK Push
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, "{$this->baseUrl}/mpesa/stkpush/v1/processrequest");
            curl_setopt($curl, CURLOPT_HTTPHEADER, $stkHeader);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $dataString);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_TIMEOUT, 30);

            $curlResponse = curl_exec($curl);
            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            $error = curl_error($curl);
            
            if ($error) {
                curl_close($curl);
                throw new \Exception("cURL Error: " . $error);
            }

            curl_close($curl);

            $responseData = json_decode($curlResponse, true);

            if ($httpCode !== 200) {
                throw new \Exception("HTTP Error: " . $httpCode . " - Response: " . $curlResponse);
            }

            if (isset($responseData['errorCode'])) {
                throw new \Exception("API Error: " . $responseData['errorMessage'] . " (Code: " . $responseData['errorCode'] . ")");
            }

            Log::info('STK Push Response', $responseData);
            
            return $responseData;

        } catch (\Exception $e) {
            Log::error('STK Push Failed', [
                'error' => $e->getMessage(),
                'paybill' => $paybill->paybill_number,
                'phone' => $phone,
                'amount' => $amount
            ]);
            
            throw $e;
        }
    }

    /**
     * Get access token using cURL
     */
    private function getAccessToken($consumerKey, $consumerSecret)
    {
        $headers = ['Content-Type: application/json; charset=utf8'];
        
        $curl = curl_init("{$this->baseUrl}/oauth/v1/generate?grant_type=client_credentials");
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_USERPWD, $consumerKey . ':' . $consumerSecret);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_TIMEOUT, 15);

        $result = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $error = curl_error($curl);
        
        if ($error) {
            curl_close($curl);
            throw new \Exception("Access token cURL Error: " . $error);
        }

        curl_close($curl);

        if ($httpCode !== 200) {
            throw new \Exception("Access token HTTP Error: " . $httpCode . " - Response: " . $result);
        }

        $result = json_decode($result, true);
        
        if (!isset($result['access_token'])) {
            throw new \Exception("No access token in response: " . json_encode($result));
        }

        Log::debug('Access token obtained successfully');
        
        return $result['access_token'];
    }
}