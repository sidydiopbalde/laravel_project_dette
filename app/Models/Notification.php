<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = ['client_id', 'message'];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function markAsRead()
    {
        $this->update(['is_read' => true]);
    }

    public static function unreadNotifications($clientId)
    {
        return self::where('client_id', $clientId)
                    ->where('is_read', false)
                    ->get();
    }

    public static function readNotifications($clientId)
    {
        return self::where('client_id', $clientId)
                    ->where('is_read', true)
                    ->get();
    }
}

