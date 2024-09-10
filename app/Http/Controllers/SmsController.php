<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Jobs\SendDettesSmsJob;
class SmsController extends Controller
{
    /**
     * Envoyer des SMS aux clients ayant une dette non soldée.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendSms()
    {
        // Dispatchez le job
        dispatch(new SendDettesSmsJob(app()->make('App\Services\SmsService')));

        return response()->json([
            'message' => 'Les SMS sont en cours d\'envoi.'
        ], 200);
    } 
}
