<?php
namespace App\Events;

use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UsersCreated

{
    use Dispatchable, SerializesModels;

    public $user;
    public $filePath;

    public function __construct(User $user, $filePath)
    {
        $this->user = $user;
        $this->filePath = $filePath;
    }
}
