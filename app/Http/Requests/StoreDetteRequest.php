<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDetteRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            // 'montant' => 'required|numeric|min:0',
            'clientId' => 'required|exists:clients,id',
            'articles' => 'required|array',
            'articles.*.articleId' => 'required|exists:articles,id',
            'articles.*.qte' => 'required|numeric|min:0',
            'articles.*.prix_unitaire' => 'required|numeric|min:0',
            'paiement.montant' => 'nullable|numeric|min:0',
        ];
    }

    public function messages()
    {
        return [
            // 'montant.required' => 'Le montant est requis.',
            // 'montant.numeric' => 'Le montant doit être un nombre.',
            // 'montant.min' => 'Le montant doit être supérieur ou égal à 0.',
            'clientId.required' => 'L\'ID du client est requis.',
            'clientId.exists' => 'L\'ID du client doit exister dans la base de données.',
            'articles.required' => 'Les articles sont requis.',
            'articles.array' => 'Les articles doivent être sous forme de tableau.',
            'articles.*.articleId.required' => 'L\'ID de l\'article est requis.',
            'articles.*.articleId.exists' => 'L\'ID de l\'article doit exister dans la base de données.',
            'articles.*.qte.required' => 'La quantité est requise.',
            'articles.*.qte.numeric' => 'La quantité doit être un nombre.',
            'articles.*.qte.min' => 'La quantité doit être supérieure ou égale à 0.',
            'articles.*.prix_unitaire.required' => 'Le prix unitaire est requis.',
            'articles.*.prix_unitaire.numeric' => 'Le prix unitaire doit être un nombre.',
            'articles.*.prix_unitaire.min' => 'Le prix unitaire doit être supérieur ou égal à 0.',
            'paiement.montant.numeric' => 'Le montant du paiement doit être un nombre.',
            'paiement.montant.min' => 'Le montant du paiement doit être supérieur ou égal à 0.',
        ];
    }
}
