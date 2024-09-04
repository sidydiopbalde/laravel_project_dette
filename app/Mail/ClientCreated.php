<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\Client;

class ClientCreated extends Mailable
{
    use Queueable, SerializesModels;

    public $client;
    public $pdfPath;

    public function __construct(Client $client, $pdfPath)
    {
        $this->client = $client;
        $this->pdfPath = $pdfPath;
    }

    public function build()
    {
        return $this->view('emails.client_created')
                    ->subject('Votre compte a été créé')
                    ->attach($this->pdfPath, [
                        'as' => 'client_information.pdf',
                        'mime' => 'application/pdf',
                    ]);
    }
}

// namespace App\Mail;

// use Illuminate\Bus\Queueable;
// use Illuminate\Contracts\Queue\ShouldQueue;
// use Illuminate\Mail\Mailable;
// use Illuminate\Mail\Mailables\Content;
// use Illuminate\Mail\Mailables\Envelope;
// use Illuminate\Queue\SerializesModels;

// class ClientCreated extends Mailable
// {
//     use Queueable, SerializesModels;

//     /**
//      * Create a new message instance.
//      */
//     public function __construct()
//     {
//         //
//     }

//     /**
//      * Get the message envelope.
//      */
//     public function envelope(): Envelope
//     {
//         return new Envelope(
//             subject: 'Client Created',
//         );
//     }

//     /**
//      * Get the message content definition.
//      */
//     public function content(): Content
//     {
//         return new Content(
//             view: 'view.name',
//         );
//     }

//     /**
//      * Get the attachments for the message.
//      *
//      * @return array<int, \Illuminate\Mail\Mailables\Attachment>
//      */
//     public function attachments(): array
//     {
//         return [];
//     }
// }
