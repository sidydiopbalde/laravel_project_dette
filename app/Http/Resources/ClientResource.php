<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClientResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        return [
            'surnom' => $this->surnom,
            'adresse' => $this->adresse,
            'telephone' => $this->telephone,
            'user_id' => $this->user_id,
            // Ajouter d'autres champs si nécessaire
        ];

         
    }
}
