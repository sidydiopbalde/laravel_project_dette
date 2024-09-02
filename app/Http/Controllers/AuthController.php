<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * @OA\Post(
 *     path="/api/v1/login",
 *     summary="Authentifie un utilisateur et retourne un token d'accès",
 *     tags={"Auth"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"login", "password"},
 *             @OA\Property(property="login", type="string", example="user@example.com"),
 *             @OA\Property(property="password", type="string", example="password123")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Connexion réussie",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Connexion réussie"),
 *             @OA\Property(property="user", type="object", ref="#/components/schemas/User"),
 *             @OA\Property(property="token", type="string", example="Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6IkpvaG4gRG9lIiwiaWF0IjoxNTE2MjM5MDIyfQ.SflKxwRJSMeKKF2QT4fwpMeJf36POk6yJV_adQssw5c")
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Informations d'identification invalides",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Informations d'identification invalides")
 *         )
 *     ),
 *     security={{"BearerToken": {}}}
 * )
 */
class AuthController extends Controller
{
    /**
 * @OA\Schema(
 *     schema="User",
 *     type="object",
 *     required={"id", "login", "password"},
 *     @OA\Property(property="id", type="integer", format="int64", example=1),
 *     @OA\Property(property="password", type="string", example="John Doe"),
 *     @OA\Property(property="login", type="string", example="john.doe@example.com"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2021-01-01T00:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2021-01-01T00:00:00Z")
 * )
 */
    public function login(Request $request)
    {
        // Valider les informations d'identification entrées par l'utilisateur
        $credentials = $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
        ]);

        // Tentative de connexion avec les informations d'identification fournies
        if (Auth::attempt($credentials)) {
            // Si l'authentification est réussie, redirigez l'utilisateur
            $user = User::find(Auth::user()->id);
           // dd($user); // Récupérer l'utilisateur connecté
            $token = $user->createToken('LaravelPassportAuth',)->accessToken; // Créer un token si vous utilisez Laravel Passport
            
            return response()->json([
                'message' => 'Connexion réussie',
                'user' => $user,
                'token' => $token
            ], 200);
        } else {
            // Si l'authentification échoue, renvoyez une réponse avec une erreur
            return response()->json(['message' => 'Informations d\'identification invalides'], 401);
        }
    }
}
