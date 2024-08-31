<?php
namespace App\Http\Resources;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'prenom' => $this->prenom,
            'nom' => $this->nom,
            'email' => $this->email,
            'role' => $this->role,
            'user' => $this->whenLoaded('user') ? new UserResource($this->user) : null,
           
        ];

         
    }
}

