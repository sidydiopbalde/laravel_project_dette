<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\CustumPassword;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreUserRequest extends FormRequest
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
            'prenom' => 'required|string|max:255',
            'nom' => 'required|string|max:255',
            'login' => 'required|string|max:255|unique:users,login',
            'mail' => 'required|string|email|max:255|unique:users,mail',
            'password' => ['required', 'string', new CustumPassword],
            'role_id' => 'required|exists:roles,id',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048', // Validation de la photo
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
            'prenom.required' => 'Le prénom de l\'utilisateur est obligatoire.',
            'prenom.string' => 'Le prénom de l\'utilisateur doit être une chaîne de caractères.',
            'prenom.max' => 'Le prénom de l\'utilisateur ne peut pas dépasser 255 caractères.',

            'nom.required' => 'Le nom de l\'utilisateur est obligatoire.',
            'nom.string' => 'Le nom de l\'utilisateur doit être une chaîne de caractères.',
            'nom.max' => 'Le nom de l\'utilisateur ne peut pas dépasser 255 caractères.',

            'login.required' => 'Le login de l\'utilisateur est obligatoire.',
            'login.string' => 'Le login de l\'utilisateur doit être une chaîne de caractères.',
            'login.max' => 'Le login de l\'utilisateur ne peut pas dépasser 255 caractères.',
            'login.unique' => 'Le login de l\'utilisateur doit être unique.',

            'mail.required' => 'L\'email de l\'utilisateur est obligatoire.',
            'mail.string' => 'L\'email de l\'utilisateur doit être une chaîne de caractères.',
            'mail.email' => 'L\'email de l\'utilisateur doit être une adresse email valide.',
            'mail.max' => 'L\'email de l\'utilisateur ne peut pas dépasser 255 caractères.',
            'mail.unique' => 'L\'email de l\'utilisateur doit être unique.',

            'password.required' => 'Le mot de passe de l\'utilisateur est obligatoire.',

            'role_id.required' => 'Le rôle de l\'utilisateur est obligatoire.',
            'role_id.exists' => 'Le rôle sélectionné n\'existe pas.',

            'photo.image' => 'Le fichier téléchargé doit être une image.',
            'photo.mimes' => 'La photo doit être un fichier de type : jpg, jpeg, png.',
            'photo.max' => 'La taille de la photo ne peut pas dépasser 2 Mo.',
        ];
    }

    /**
     * Gère l'échec de la validation.
     *
     * @param Validator $validator
     * @throws HttpResponseException
     */
    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'message' => 'La validation a échoué.',
            'errors' => $validator->errors()
        ], 422));
    }
}
