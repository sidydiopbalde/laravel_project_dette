<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateArticlestockRequest extends FormRequest
{
    public function authorize()
    {
        // Autoriser ou non cette requête. 
        // Vous pouvez mettre en place une logique ici selon vos besoins.
        return true;
    }

    public function rules()
    {
        return [
            'articles' => 'required|array',
            'articles.*.id' => 'required|integer|exists:articles,id',
            'articles.*.qte' => 'required|integer|min:1',
        ];
    }

    public function messages()
    {
        return [
            'articles.required' => 'La liste des articles est obligatoire.',
            'articles.array' => 'Les articles doivent être un tableau.',
            'articles.*.id.required' => 'L\'identifiant de l\'article est obligatoire.',
            'articles.*.id.integer' => 'L\'identifiant de l\'article doit être un entier.',
            'articles.*.id.exists' => 'L\'article spécifié n\'existe pas.',
            'articles.*.qte.required' => 'La quantité de l\'article est obligatoire.',
            'articles.*.qte.integer' => 'La quantité de l\'article doit être un entier.',
            'articles.*.qte.min' => 'La quantité de l\'article doit être au moins 1.',
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
