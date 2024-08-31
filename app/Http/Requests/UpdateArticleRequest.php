<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateArticleRequest extends FormRequest
{
    
    // Détermine si l'utilisateur est autorisé à faire cette requête.
     
    public function authorize(): bool
    {
        return true;
    }

    
     //Règles de validation qui s'appliquent à la requête.
     
    public function rules(): array
    {
        return [
            'qte' => 'required|integer|min:1',
        ];
    }

    /**
     * Messages personnalisés pour les erreurs de validation.
     */
    public function messages(): array
    {
        return [
            'qte.required' => 'La quantité en stock est requise.',
            'qte.integer' => 'La quantité en stock doit être un nombre entier.',
            'qte.min' => 'La quantité en stock doit être supérieure à 0.',
        ];
    }
    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'message' => 'La validation a échoué.',
            'errors' => $validator->errors()
        ], 422));
    // return response()->json(['message' => 'message'], 422);
    }
}