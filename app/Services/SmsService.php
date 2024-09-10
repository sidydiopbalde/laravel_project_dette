<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Twilio\Rest\Client;

class SmsService
{
    protected $twilio;

    public function __construct()
    {
        $sid = env('TWILIO_SID');
        $token = env('TWILIO_AUTH_TOKEN');
        
        if (!$sid || !$token) {
            throw new \Exception('Twilio credentials are not set.');
        }
        
        // Log::info('salut');
        $this->twilio = new Client($sid, $token);
        // dd($sid,$token,$this->twilio, env('TWILIO_PHONE_NUMBER'));
    }
    
    public function sendSms($to, $message)
    {
        // dd($to,$message);
        try {
            $this->twilio->messages->create($to, [
                'from' => env('TWILIO_PHONE_NUMBER'),
                'body' => $message
            ]);
        } catch (\Exception $e) {
            return $e->getMessage();
        }

        return true;
    }
}

