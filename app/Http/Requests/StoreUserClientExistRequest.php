<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Rules\TelephoneRules; // Assurez-vous d'inclure vos règles personnalisées si nécessaire

class StoreUserClientExistRequest extends FormRequest
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
            'password' => ['required', 'string', 'min:8'], // Assurez-vous que le mot de passe respecte les exigences
            'mail' => 'required|string|email|max:255|unique:users,mail',
            'role_id' => ['required', 'exists:roles,id'], // Assurez-vous que role_id existe dans la table roles
            'photo' => 'nullable|file|mimes:jpeg,png,jpg|max:2048', // Exemple pour une image
            'client_id' => ['required', 'exists:clients,id'], // Assurez-vous que client_id existe dans la table clients
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
            'prenom.required' => 'Le prénom est obligatoire.',
            'prenom.string' => 'Le prénom doit être une chaîne de caractères.',
            'prenom.max' => 'Le prénom ne peut pas dépasser 255 caractères.',
            
            'nom.required' => 'Le nom est obligatoire.',
            'nom.string' => 'Le nom doit être une chaîne de caractères.',
            'nom.max' => 'Le nom ne peut pas dépasser 255 caractères.',
            
            'login.required' => 'Le login est obligatoire.',
            'login.string' => 'Le login doit être une chaîne de caractères.',
            'login.max' => 'Le login ne peut pas dépasser 255 caractères.',
            'login.unique' => 'Le login est déjà utilisé.',
            
            'password.required' => 'Le mot de passe est obligatoire.',
            'password.string' => 'Le mot de passe doit être une chaîne de caractères.',
            'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
            
            'mail.required' => 'L\'email est obligatoire.',
            'mail.email' => 'L\'email doit être une adresse email valide.',
            'mail.max' => 'L\'email ne peut pas dépasser 255 caractères.',
            'mail.unique' => 'L\'email est déjà utilisé.',
            
            'role_id.required' => 'Le rôle est obligatoire.',
            'role_id.exists' => 'Le rôle spécifié n\'existe pas.',
            
            'photo.file' => 'La photo doit être un fichier.',
            'photo.mimes' => 'La photo doit être au format jpeg, png, ou jpg.',
            'photo.max' => 'La photo ne peut pas dépasser 2 Mo.',
            
            'client_id.required' => 'L\'ID du client est obligatoire.',
            'client_id.exists' => 'Le client spécifié n\'existe pas.',
        ];
    }
}
