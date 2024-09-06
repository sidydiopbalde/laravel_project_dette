<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ClientCreated extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $pdfPath;

    public function __construct(User $user, $pdfPath)
    {
        $this->user = $user;
        $this->pdfPath = $pdfPath;
    }

    public function build()
    {
        return $this->view('emails.client_created')
                    ->with(['user' => $this->user->mail])
                    ->attach($this->pdfPath, [
                        'as' => 'client_' . $this->user->id . '.pdf',
                        'mime' => 'application/pdf',
                    ]);
    }
}
