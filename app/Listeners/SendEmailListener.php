<?php

namespace App\Listeners;

use App\Events\UserCreated;
use App\Jobs\SendClientEmailJob;
use App\Services\PdfService;
use App\Services\QrCodeService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class SendEmailListener implements ShouldQueue
{
    // use InteractsWithQueue, SerializesModels;

    public function handle(UserCreated $event)
    {
        try {
            // Génération du QR code
            $qrCodeData = json_encode([
                'name' => $event->user->nom,
                'email' => $event->user->mail,
                'telephone' => $event->user->telephone,
            ]);

            $qrCodeFileName = 'client_' . $event->user->id . '.png';
            $qrCodePath = app(QrCodeService::class)->generateQrCode($qrCodeData, $qrCodeFileName);
            
            $pdfPath = storage_path('public/pdfs/client_' . $event->user->id . '.pdf');
            
            app(PdfService::class)->generatePdf('pdf.client', [
                'user' => $event->user, 
                'qrCodePath' => $qrCodePath
            ], $pdfPath);
            
            // Dispatch du job pour envoyer l'e-mail avec le PDF
            SendClientEmailJob::dispatch($event->user, $pdfPath);

        } catch (\Exception $e) {
            Log::error('Erreur lors de la génération du QR code et du PDF : ' . $e->getMessage());
        }
    }
}
