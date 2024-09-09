<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DetteRequest extends FormRequest
{
    /**
     * Détermine si l'utilisateur est autorisé à faire cette requête.
     *
     * @return bool
     */
    public function authorize()
    {
        return true; // Changez ceci en fonction des besoins de votre application
    }

    /**
     * Obtenez les règles de validation pour la requête.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'montant' => 'required|numeric|min:0',
            'montant_due' => 'required|numeric|min:0',
            'client_id' => 'required|numeric|min:0',
        ];
    }

    /**
     * Obtenez les messages de validation personnalisés.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'montant.required' => 'Le montant est requis.',
            'montant.numeric' => 'Le montant doit être un nombre.',
            'montant.min' => 'Le montant doit être supérieur ou égal à 0.',
            
    
        ];
    }
}
