<?php


namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Users;
 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Enums\SuccessEnum;
use App\Http\Requests\StoreRequest;
use App\Http\Requests\StoreUserClientExistRequest;
use App\Traits\ApiResponseTrait;
use App\Http\Requests\StoreUserRequest;
use App\Http\Resources\UserResource;
use App\Models\Clients;
use Laravel\Passport\HasApiTokens ;
use Illuminate\Support\Facades\DB;


/**
 * @OA\Schema(
 *     schema="User",
 *     type="object",
 *     required={"id", "prenom", "nom", "mail", "login"},
 *     @OA\Property(property="id", type="integer", format="int64", example=1),
 *     @OA\Property(property="prenom", type="string", example="John"),
 *     @OA\Property(property="nom", type="string", example="Doe"),
 *     @OA\Property(property="mail", type="string", example="john.doe@example.com"),
 *     @OA\Property(property="login", type="string", example="johndoe"),
 *     @OA\Property(property="photo", type="string", example="path/to/photo.jpg"),
 *     @OA\Property(property="role_id", type="integer", format="int64", example=3),
 *     @OA\Property(property="active", type="boolean", example=true),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2021-01-01T00:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2021-01-01T00:00:00Z")
 * )
 */
/**
 * @OA\Schema(
 *     schema="UserWithClient",
 *     type="object",
 *     required={"prenom", "nom", "mail", "login", "password", "client_id"},
 *     @OA\Property(property="prenom", type="string", example="John"),
 *     @OA\Property(property="nom", type="string", example="Doe"),
 *     @OA\Property(property="mail", type="string", example="john.doe@example.com"),
 *     @OA\Property(property="login", type="string", example="johndoe"),
 *     @OA\Property(property="password", type="string", example="password123"),
 *     @OA\Property(property="client_id", type="integer", format="int64", example=1),
 *     @OA\Property(property="photo", type="string", example="path/to/photo.jpg"),
 * )
 */
class UserController extends Controller
{
    use ApiResponseTrait;
    // Affiche la liste de tous les utilisateurs
        /**
     * @OA\Get(
     *     path="/api/v1/users",
     *     summary="List all users",
     *     tags={"Users"},
     *     @OA\Parameter(name="role", in="query", description="Filter users by role", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="active", in="query", description="Filter users by active status", required=false, @OA\Schema(type="string", enum={"oui", "non"})),
     *     @OA\Response(
     *         response=200,
     *         description="List of users",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/User"))
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No users found"
     *     )
     * )
     */
    public function index(Request $request)
    {
        // Initialiser la requête pour récupérer les utilisateurs
        $query = User::query();
    
        // Filtrer par rôle si le paramètre role est présent dans la requête
        if ($request->has('role')) {
            $query->where('role_id', $request->query('role'));
        }
    
        // Filtrer par statut actif si le paramètre active est présent dans la requête
        if ($request->has('active')) {
            $active = $request->query('active');
            
            if ($active === 'oui') {
                $query->where('active', true);
            } elseif ($active === 'non') {
                $query->where('active', false);
            }
        }
    
        // Récupérer les utilisateurs filtrés
        $users = $query->get();
    
        // Vérifier si la collection est vide
        if ($users->isEmpty()) {
            return response()->json([
                'status' => 404,
                'message' => 'Aucun utilisateur trouvé.'
            ], 404);
        }
    
        // Retourner les utilisateurs trouvés
        return response()->json([ 'status' => 200,'date'=>$users, 'message' => 'liste des utilisateur']);
    }
    
    
    
    
    /**
     * @OA\Get(
     *     path="/api/v1/users/{id}",
     *     summary="Get a user by ID",
     *     tags={"Users"},
     *     @OA\Parameter(name="id", in="path", description="ID of the user", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(
     *         response=200,
     *         description="User details",
     *         @OA\JsonContent(ref="#/components/schemas/User")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found"
     *     )
     * )
     */

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
   /**
     * @OA\Post(
     *     path="/api/registerUser",
     *     summary="Create a new user",
     *     tags={"Users"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/User")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/User")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid input"
     *     )
     * )
     */
//enregister un utilisateur spécifique
    public function store(StoreUserRequest $request)
    {
        $validatedData = $request->validated();
    
        // Gestion du fichier de la photo
        if ($request->hasFile('photo')) {
            $filePath = $request->file('photo')->store('photos', 'public');
            $validatedData['photo'] = $filePath;
        }
    
        // Créer l'utilisateur avec toutes les données validées, y compris la photo
        $user = User::create([
            'prenom' => $validatedData['prenom'],
            'nom' => $validatedData['nom'],
            'mail' => $validatedData['mail'],
            'login' => $validatedData['login'],
            'password' => Hash::make($validatedData['password']),
            'role_id' => $validatedData['role_id'],
            'photo' => $validatedData['photo'] ?? null, // Stocke le chemin de la photo s'il existe
        ]);
        $user->save();
        return $this->sendResponse(200, new UserResource($user), 'Utilisateur créé avec succès.');
    }
    

    /**
     * @OA\Post(
     *     path="/api/v1/users/register",
     *     summary="Create a new user and associate with a client",
     *     tags={"Users"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/UserWithClient")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User created and associated with client",
     *         @OA\JsonContent(ref="#/components/schemas/User")
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error during creation"
     *     )
     * )
     */
    //enregiser user avec un client
    public function storeUserClientExist(StoreUserClientExistRequest $request)
    {
        // dd($request->all());
       
        try {
            // Début de la transaction
            DB::beginTransaction();
    
            // Validation des données
            $validatedData = $request->validated();
    
            // Création de l'utilisateur
            $userData = [
                'prenom' => $validatedData['prenom'],
                'nom' => $validatedData['nom'],
                'password' => bcrypt($validatedData['password']),
                'login' => $validatedData['login'],
                'mail' => $validatedData['mail'],
                'role_id' => 3,
                'photo' => $request->hasFile('photo') ? $request->file('photo')->store('photos', 'public') : null,
            ];
            // dd($userData);
            // Création de l'utilisateur
            $user = User::create($userData);
    
            // Association du client à l'utilisateur
            $clientId = $validatedData['client_id'];
            $client = Clients::find($clientId);
    
            if (!$client) {
                throw new \Exception('Client non trouvé.');
            }
    
            $client->user()->associate($user);
            $client->save();
    
            // Validation de la transaction
            DB::commit();
    
            return $this->sendResponse(200, new UserResource($user), 'Utilisateur créé et associé au client avec succès.');
        } catch (\Exception $e) {
            // En cas d'erreur, annuler la transaction
            DB::rollBack();
    
            return $this->sendResponse(500, null, 'Erreur lors de la création de l\'utilisateur: ' . $e->getMessage());
        }
    }
        /**
     * @OA\Put(
     *     path="/api/v1/users/{id}",
     *     summary="Update an existing user",
     *     tags={"Users"},
     *     @OA\Parameter(name="id", in="path", description="ID of the user to update", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/User")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/User")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found"
     *     )
     * )
     */
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
   /**
     * @OA\Delete(
     *     path="/api/v1/users/{id}",
     *     summary="Delete a user",
     *     tags={"Users"},
     *     @OA\Parameter(name="id", in="path", description="ID of the user to delete", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(
     *         response=200,
     *         description="User deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found"
     *     )
     * )
     */
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
