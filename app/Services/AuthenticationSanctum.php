<?php
namespace App\Services;

use Illuminate\Support\Facades\Auth;
use App\Services\AuthentificationServiceInterface;
use App\Models\User;

class AuthenticationSanctum implements AuthentificationServiceInterface
{
    public function authenticate(array $credentials)
    {
        // Tentative d'authentification
        if (!Auth::attempt($credentials)) {
            // Récupération de l'utilisateur authentifié
            throw new \Exception;
        }
        $user=User::where('login',$credentials['login'])->firstOrFail();
        $token = $user->createToken('personal_access_token')->plainTextToken;
        return [
        'user' => $user,
        'token' => $token
    ];
    }

    public function logout()
    {
        // Récupère l'utilisateur actuellement authentifié et révoque tous les tokens
        $user = User::find(Auth::user()->id);
        if ($user) {
            $user->tokens()->delete();
        }
    }
}

