<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\CustumPassword;
use App\Rules\TelephoneRules;
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
            'telephone' => ['required', 'string', 'max:15', 'unique:clients', new TelephoneRules()],
            'photo' => 'nullable|image|mimes:jpg,jpeg,png,svg|max:2048', // Validation pour la photo
            'user' => ['sometimes'],
            'user.prenom' => 'required|string|max:255',
            'user.nom' => 'required|string|max:255',
            'user.login' => 'required|string|max:255|unique:users,login',
            'user.role_id' => 'required|exists:roles,id', // Validation pour role_id
            'user.mail' => 'nullable|string|email|max:255|unique:users,mail',
            'user.password' => ['nullable', new CustumPassword],
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
            
            'photo.image' => 'La photo doit être une image.',
            'photo.mimes' => 'La photo doit être au format jpg, jpeg, ou png.',
            'photo.max' => 'La photo ne peut pas dépasser 2 Mo.',
            
            'user.role_id.required' => 'Le rôle de l\'utilisateur est obligatoire.',
            'user.role_id.exists' => 'Le rôle sélectionné n\'existe pas.',

            'user.prenom.string' => 'Le prénom de l\'utilisateur doit être une chaîne de caractères.',
            'user.nom.string' => 'Le nom de l\'utilisateur doit être une chaîne de caractères.',
            'user.login.string' => 'Le login de l\'utilisateur doit être une chaîne de caractères.',
            'user.login.unique' => 'Le login de l\'utilisateur doit être unique.',
            'user.role_id.exists' => 'Le rôle sélectionné n\'existe pas.',
            'user.mail.email' => 'L\'email de l\'utilisateur doit être une adresse email valide.',
            'user.mail.unique' => 'L\'email de l\'utilisateur doit être unique.',
            'user.password.min' => 'Le mot de passe de l\'utilisateur doit contenir au moins 8 caractères.',
        ];
    }

    /**
     * Gère l'échec de la validation.
     *
     * @param Validator $validator
     * @return \Illuminate\Http\JsonResponse
     */
    public function failedValidation(Validator $validator)
    {
        return response()->json(['message' => 'La validation a échoué.', 'errors' => $validator->errors()], 422);
    }
}
