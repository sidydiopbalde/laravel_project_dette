<?php

namespace App\Services;

interface SmsNotificationServiceInterface
{
    public function sendSms(string $to, string $message);
    public function notifyClientsWithDebts();
}