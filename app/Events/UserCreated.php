<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserCreated
{
    use Dispatchable, SerializesModels;

    public $user;
    public $photo;

    /**
     * Create a new event instance.
     *
     * @param \App\Models\User $user
     * @param mixed $photo
     */
    public function __construct(User $user, $photo)
    {
        $this->user = $user;
        $this->photo = $photo;
     
        // dd($this->photo);
    }
    /**
     * Get the channels the event should broadcast on.
     *
     * @return array
     */

}
