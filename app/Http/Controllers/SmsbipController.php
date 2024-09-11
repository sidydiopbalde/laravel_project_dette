<?php
namespace App\Http\Controllers;

use App\Services\SmsServiceInterface;
use Illuminate\Http\Request;

class SmsbipController extends Controller
{
    protected $smsService;

    public function __construct(SmsServiceInterface $smsService)
    {
        $this->smsService = $smsService;
    }

    public function sendSms(Request $request)
    {
        // $validated = $request->validate([
        //     'to' => 'required|string',
        //     'message' => 'required|string',
        // ]);

        // $toNumber = $validated['to'];
        $toNumber="+221784316538";
        // $message = $validated['message'];
        $message = "test infobip by Sidy Diop";

        try {
            $this->smsService->sendSMS($toNumber, $message);
            return response()->json(['message' => 'SMS envoyé avec succès'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur lors de l\'envoi du SMS'], 500);
        }
    }
}
