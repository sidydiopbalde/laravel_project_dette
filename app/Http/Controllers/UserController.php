<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\StoreUserClientExistRequest;
use App\Http\Resources\UserResource;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
// /**
//  * @OA\Schema(
//  *     schema="User",
//  *     type="object",
//  *     required={"id", "prenom", "nom", "mail", "login"},
//  *     @OA\Property(property="id", type="integer", format="int64", example=1),
//  *     @OA\Property(property="prenom", type="string", example="John"),
//  *     @OA\Property(property="nom", type="string", example="Doe"),
//  *     @OA\Property(property="mail", type="string", example="john.doe@example.com"),
//  *     @OA\Property(property="login", type="string", example="johndoe"),
//  *     @OA\Property(property="photo", type="string", example="path/to/photo.jpg"),
//  *     @OA\Property(property="role_id", type="integer", format="int64", example=3),
//  *     @OA\Property(property="active", type="boolean", example=true),
//  *     @OA\Property(property="created_at", type="string", format="date-time", example="2021-01-01T00:00:00Z"),
//  *     @OA\Property(property="updated_at", type="string", format="date-time", example="2021-01-01T00:00:00Z")
//  * )
//  */
// /**
//  * @OA\Schema(
//  *     schema="UserWithClient",
//  *     type="object",
//  *     required={"prenom", "nom", "mail", "login", "password", "client_id"},
//  *     @OA\Property(property="prenom", type="string", example="John"),
//  *     @OA\Property(property="nom", type="string", example="Doe"),
//  *     @OA\Property(property="mail", type="string", example="john.doe@example.com"),
//  *     @OA\Property(property="login", type="string", example="johndoe"),
//  *     @OA\Property(property="password", type="string", example="password123"),
//  *     @OA\Property(property="client_id", type="integer", format="int64", example=1),
//  *     @OA\Property(property="photo", type="string", example="path/to/photo.jpg"),
//  * )
//  */
class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

//     /**
//  * @OA\Get(
//  *     path="/api/v1/users",
//  *     summary="List all users",
//  *     tags={"Users"},
//  *     @OA\Parameter(
//  *         name="role",
//  *         in="query",
//  *         description="Filter users by role",
//  *         required=false,
//  *         @OA\Schema(type="string")
//  *     ),
//  *     @OA\Parameter(
//  *         name="active",
//  *         in="query",
//  *         description="Filter users by active status",
//  *         required=false,
//  *         @OA\Schema(type="string", enum={"oui", "non"})
//  *     ),
//  *     @OA\Response(
//  *         response=200,
//  *         description="List of users",
//  *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/User"))
//  *     ),
//  *     @OA\Response(
//  *         response=404,
//  *         description="No users found"
//  *     )
//  * )
//  */
    public function index(Request $request)
    {
        $filters = $request->only(['role', 'active']);
        $users = $this->userService->getAllUsers($filters);
        return $users;
    }
    
//    /**
//      * @OA\Post(
//      *     path="/api/registerUser",
//      *     summary="Create a new user",
//      *     tags={"Users"},
//      *     @OA\RequestBody(
//      *         required=true,
//      *         @OA\JsonContent(ref="#/components/schemas/User")
//      *     ),
//      *     @OA\Response(
//      *         response=200,
//      *         description="User created successfully",
//      *         @OA\JsonContent(ref="#/components/schemas/User")
//      *     ),
//      *     @OA\Response(
//      *         response=400,
//      *         description="Invalid input"
//      *     )
//      * )
//      */
    public function store(StoreUserRequest $request)
    {
        $validatedData = $request->validated();
        
        $user = $this->userService->createUser($validatedData);
        return $user;
    }

    // /**
    //  * @OA\Post(
    //  *     path="/api/v1/register",
    //  *     summary="Create a new user and associate with a client",
    //  *     tags={"Users"},
    //  *     @OA\RequestBody(
    //  *         required=true,
    //  *         @OA\JsonContent(ref="#/components/schemas/UserWithClient")
    //  *     ),
    //  *     @OA\Response(
    //  *         response=200,
    //  *         description="User created and associated with client",
    //  *         @OA\JsonContent(ref="#/components/schemas/User")
    //  *     ),
    //  *     @OA\Response(
    //  *         response=500,
    //  *         description="Error during creation"
    //  *     )
    //  * )
    //  */
    public function storeUserClientExist(StoreUserClientExistRequest $request)
    {
        $validatedData = $request->validated();

        // Ajout de la logique pour gérer l'upload de la photo
        if ($request->hasFile('photo')) {
            $validatedData['photo'] = $request->file('photo')->store('photos', 'public');
        }

    
            $user = $this->userService->storeUserClientExist($validatedData);
            return $user;
        
    }
    //     /**
    //  * @OA\Get(
    //  *     path="/api/v1/users/{id}",
    //  *     summary="Get a user by ID",
    //  *     tags={"Users"},
    //  *     @OA\Parameter(name="id", in="path", description="ID of the user", required=true, @OA\Schema(type="integer")),
    //  *     @OA\Response(
    //  *         response=200,
    //  *         description="User details",
    //  *         @OA\JsonContent(ref="#/components/schemas/User")
    //  *     ),
    //  *     @OA\Response(
    //  *         response=404,
    //  *         description="User not found"
    //  *     )
    //  * )
    //  */
    public function show($id)
    {
            $user = $this->userService->getUserById($id);
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
