<?php


namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Users;
 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Enums\SuccessEnum;
use App\Traits\ApiResponseTrait;
use App\Http\Requests\StoreUserRequest;
use App\Http\Resources\UserResource;
use Laravel\Passport\HasApiTokens ;
class UserController extends Controller
{
    use ApiResponseTrait;
    // Affiche la liste de tous les utilisateurs
    public function index()
    {
        $users = User::all(); // Récupérer tous les utilisateurs
        // dd($users);
        return response()->json($users, 200);
    }


    // Affiche un utilisateur spécifique par son ID
    public function show($id)
    {
        $id = (int) $id;
        // dd($id);
        $user = Users::find(10);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
        return $this->sendResponse(200, new UserResource($user), 'L\'utilisateurs récupérée avec succès.');
    }


    public function store(StoreUserRequest $request)
    {

        $validatedData = $request->validated();
        $user = User::create([
            'prenom' => $validatedData['prenom'],
            'nom' => $validatedData['nom'],
            'mail' => $validatedData['mail'],
            'login' => $validatedData['login'],
            'password' => Hash::make($validatedData['password']),
            'role' => $validatedData['role'],
        ]);
       
          $user->save();
        return $this->sendResponse(200,new UserResource($user), 'User crée  avec succès.');
    }
    // Met à jour un utilisateur existant
    public function update(Request $request, $id)
    {
        $Users = User::find($id);
        if (!$Users) {
            return response()->json(['message' => 'Users not found'], 404);
        }

        $validatedData = $request->validate([
            'prenom' => 'sometimes|string|max:255',
            'nom' => 'sometimes|string|max:255',
            'login' => 'sometimes|string|max:255|unique:Users,login,' . $id,
            'password' => 'sometimes|string|min:8',
            'role' => 'sometimes|in:ADMIN,Boutiquier,Client',
        ]);

        if (isset($validatedData['password'])) {
            $validatedData['password'] = Hash::make($validatedData['password']);
        }

        $Users->update($validatedData);

        return response()->json($Users);
    }

    // Supprime un utilisateur
    public function destroy($id)
    {
        $Users = User::find($id);
        if (!$Users) {
            return response()->json(['message' => 'Users not found'], 404);
        }

        $Users->delete();
        return response()->json(['message' => 'Users deleted successfully']);
    }

   
}
