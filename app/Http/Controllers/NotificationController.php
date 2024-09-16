<?php
// app/Http/Controllers/NotificationController.php
namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Notification;
use App\Services\ClientServiceImpl;
use App\Services\NotificationService;
use App\Services\SmsNotificationService;
use App\Services\SmsNotificationServiceInterface;
use App\Services\SmsServiceFactory;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function sendNotificationsToAllClients(Request $request)
    {
        $this->notificationService->sendNotificationsToAllClients();

        return response()->json(['message' => 'Notifications envoyées avec succès.']);
    }

    public function sendNotification($clientId)
    {
        $result = $this->notificationService->sendDebtReminder($clientId);
      

        if ($result['success']) {
            return response()->json(['message' => $result['message']]);
        }

        return response()->json(['message' => $result['message']], 204);
    }
    public function sendMessageToClients(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        $message = $request->input('message');
        $clients = $this->getClients(); 

        foreach ($clients as $client) {
            // Enregistrement de la notification en base de données
            Notification::create([
                'client_id' => $client->id,
                'message' => $message,
            ]);

            // Envoi de la notification via SMS
            $smsService = SmsServiceFactory::make();
            $smsService->sendSms($client->telephone, $message);
        }

        return response()->json(['status' => 'Notifications sent successfully']);
    }

    private function getClients()
    {
        // Implémentez la logique pour récupérer les clients. Voici un exemple simplifié :
        return \App\Models\Client::all();
    }

      // public function sendReminderToAllClients()
    // {
    //     try {
    //         $this->smsNotificationService->notifyClientsWithDebts();

    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Notifications envoyées avec succès.',
    //         ], 200);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Erreur lors de l\'envoi des notifications.',
    //             'details' => $e->getMessage(),
    //         ], 500);
    //     }
    // }
}

