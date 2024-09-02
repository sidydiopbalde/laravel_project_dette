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
            'mail' => $this->mail,
            // 'role' => $this->user->role->libelle,
            'photo' => $this->photo,
            //'client' => $this->whenLoaded('client')? new ClientResource($this->client) : null, // Indique si le client est lié à l'utilisateur et le renvoie si c'est le cas.
            'user' => $this->whenLoaded('user') ? new UserResource($this->user) : null,
           
        ];

         
    }
}

