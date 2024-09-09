<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\StoreUserClientExistRequest;
use App\Http\Resources\UserResource;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function index(Request $request)
    {
        $filters = $request->only(['role', 'active']);
        $users = $this->userService->getAllUsers($filters);
        return $users;
    }

    public function show($id)
    {
            $user = $this->userService->getUserById($id);
            return $user;
    }

    public function store(StoreUserRequest $request)
    {
        $validatedData = $request->validated();
        $user = $this->userService->createUser($validatedData);
        return $user;
    }

    public function update(Request $request, $id)
    {
      
            $validatedData = $request->validate([
                'prenom' => 'sometimes|string|max:255',
                'nom' => 'sometimes|string|max:255',
                'login' => 'sometimes|string|max:255|unique:users,login,' . $id,
                'password' => 'sometimes|string|min:8',
                'role' => 'sometimes|in:ADMIN,Boutiquier,Client',
            ]);

            $user = $this->userService->updateUser($id, $validatedData);
            return  $user;
       
    }

    public function destroy($id)
    {
        try {
            $user = $this->userService->deleteUser($id);
            return response()->json(['status' => 200, 'message' => 'Utilisateur supprimé avec succès.']);
        } catch (\Exception $e) {
            return response()->json(['status' => 404, 'message' => $e->getMessage()], 404);
        }
    }
}
