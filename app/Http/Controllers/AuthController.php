<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
       
        // Valider les informations d'identification entrées par l'utilisateur
        $credentials = $request->validate([
            'mail' => 'required|string',
            'password' => 'required|string',
        ]);

        // Tentative de connexion avec les informations d'identification fournies
        if (Auth::attempt($credentials)) {
            // Si l'authentification est réussie, redirigez l'utilisateur
            $user = User::find(Auth::user()->id); // Récupérer l'utilisateur connecté
            $token = $user->createToken('LaravelPassportAuth')->accessToken; // Créer un token si vous utilisez Laravel Passport

            return response()->json([
                'message' => 'Connexion réussie',
                'user' => $user,
                'token' => $token
            ], 200);
        }

        // Si l'authentification échoue, renvoyez une réponse avec une erreur
        return response()->json(['message' => 'Informations d\'identification invalides'], 401);
   
    }
}
