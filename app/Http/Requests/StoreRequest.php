<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\CustumPassword; 
use App\Rules\TelephoneRules; 
use App\Enums\RoleEnum;
use Illuminate\Contracts\Validation\Validator;

class StoreRequest extends FormRequest
{
    /**
     * Détermine si l'utilisateur est autorisé à effectuer cette demande.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true; // Permet la requête, à ajuster selon vos besoins
    }

    /**
     * Obtenez les règles de validation qui s'appliquent à la demande.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'surnom' => 'required|string|max:255|unique:clients',
            'adresse' => 'required|string|max:255',
            'telephone' => ['required', 'string', 'max:15', 'unique:clients', new TelephoneRules()], // Utilisation de la règle TelephoneRules
            'user' => ['sometimes'],
            'user.prenom' => 'required|string|max:255',
            'user.nom' => 'required|string|max:255',
            'user.login' => 'required|string|max:255|unique:users,login',
            'user.role' => ['required', 'string', 'in:' . implode(',', array_column(RoleEnum::cases(), 'value'))], // Utilisation de l'énumération pour les rôles
            'user.mail' => 'nullable|string|email|max:255|unique:users,mail',
            'user.password' =>  ['nullable', new CustumPassword],
        ];
    }

    /**
     * Obtenez les messages de validation personnalisés.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'surnom.required' => 'Le surnom est obligatoire.',
            'surnom.string' => 'Le surnom doit être une chaîne de caractères.',
            'surnom.max' => 'Le surnom ne peut pas dépasser 255 caractères.',
            'surnom.unique' => 'Le surnom doit être unique.',
            
            'adresse.required' => 'L\'adresse est obligatoire.',
            'adresse.string' => 'L\'adresse doit être une chaîne de caractères.',
            'adresse.max' => 'L\'adresse ne peut pas dépasser 255 caractères.',
            
            'telephone.required' => 'Le numéro de téléphone est obligatoire.',
            'telephone.string' => 'Le numéro de téléphone doit être une chaîne de caractères.',
            'telephone.max' => 'Le numéro de téléphone ne peut pas dépasser 15 caractères.',
            'telephone.unique' => 'Le numéro de téléphone doit être unique.',
            
            'user_id.exists' => 'L\'utilisateur associé n\'existe pas.',

            'user.prenom.string' => 'Le prénom de l\'utilisateur doit être une chaîne de caractères.',
            'user.nom.string' => 'Le nom de l\'utilisateur doit être une chaîne de caractères.',
            'user.login.string' => 'Le login de l\'utilisateur doit être une chaîne de caractères.',
            'user.login.unique' => 'Le login de l\'utilisateur doit être unique.',
            'user.role.in' => 'Le rôle de l\'utilisateur doit être parmi les valeurs suivantes : ADMIN, Boutiquier, Client.',
            'user.mail.email' => 'L\'email de l\'utilisateur doit être une adresse email valide.',
            'user.mail.unique' => 'L\'email de l\'utilisateur doit être unique.',
            'user.password.min' => 'Le mot de passe de l\'utilisateur doit contenir au moins 8 caractères.',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        return response()->json(['message' => 'message'], 422);
    }
}
