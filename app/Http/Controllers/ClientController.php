<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\Users;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\StoreRequest;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\QueryBuilder;
use App\Http\Requests\StoreUserRequest;
use App\Http\Resources\ClientResource;
use App\Http\Resources\UserResource;
use App\Traits\ApiResponseTrait;
use App\Facades\ClientServiceFacade;
use App\Models\User;
use App\Facades\ImageUploadFacade;
use GuzzleHttp\Client as GuzzleHttpClient;

/**
 * @OA\Schema(
 *     schema="Client",
 *     type="object",
 *     title="Clients",
 *     required={"id", "surnom", "adresse", "telephone"},
 *     @OA\Property(property="id", type="integer", format="int64", example=1),
 *     @OA\Property(property="surnom", type="string", example="ClientSurnom"),
 *     @OA\Property(property="adresse", type="string", example="ClientAdresse"),
 *     @OA\Property(property="telephone", type="string", example="784316538"),
 *     @OA\Property(property="user_id", type="integer", format="int64", example=1),
 *     @OA\Property(property="user", type="object", ref="#/components/schemas/User")
 * )
 */

class ClientController extends Controller
{
    use ApiResponseTrait;

  
 

        /**
 * @OA\Get(
 *     path="/api/clients",
 *     summary="Récupérer tous les clients",
 *     tags={"Clients"},
 *     @OA\Parameter(
 *         name="telephone",
 *         in="query",
 *         required=false,
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\Parameter(
 *         name="surnom",
 *         in="query",
 *         required=false,
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\Parameter(
 *         name="include",
 *         in="query",
 *         required=false,
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\Parameter(
 *         name="comptes",
 *         in="query",
 *         required=false,
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\Parameter(
 *         name="active",
 *         in="query",
 *         required=false,
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Liste des clients récupérée avec succès",
 *         @OA\JsonContent(
 *             type="array",
 *             @OA\Items(ref="#/components/schemas/Client")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Aucun client trouvé",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="integer", example=404),
 *             @OA\Property(property="message", type="string", example="Aucun client trouvé")
 *         )
 *     ),
 *     security={{"BearerToken": {}}}
 * )
 */
        // public function index(Request $request)
        // {
        //     // Récupération des paramètres de requête
        //     $telephones = $request->query('telephone');
        //     $surnom = $request->query('surnom');
        //     $includeUser = $request->query('include') === 'user';
        //     $comptes = $request->query('comptes'); // Récupération du paramètre comptes
        //     $active = $request->query('active'); // Récupération du paramètre active
        
        //     // Construction de la requête
        //     $query = Client::query();
        
        //     // Jointure avec la table 'users' pour accéder à la colonne 'active'
        //     $query->leftJoin('users', 'clients.user_id', '=', 'users.id');
        
        //     // Sélectionner les colonnes nécessaires
        //     $query->select('clients.*', 'users.active');
        
        //     // Application des filtres si des numéros de téléphone ou un surnom sont fournis
        //     if ($telephones) {
        //         if (!is_array($telephones)) {
        //             $telephones = explode(',', $telephones);
        //         }
        //         $query->whereIn('clients.telephone', $telephones);
        //     }
        
        //     if ($surnom) {
        //         $query->where('clients.surnom', 'like', '%' . $surnom . '%');
        //     }
        
        //     // Filtrer les clients ayant des comptes associés selon la valeur du paramètre 'comptes'
        //     if ($comptes) {
        //         if ($comptes === 'oui') {
        //             $query->whereNotNull('clients.user_id');
        //         } elseif ($comptes === 'non') {
        //             $query->whereNull('clients.user_id');
        //         }
        //     }
        
        //     // Filtrer les clients selon le paramètre 'active'
        //     if ($active) {
        //         if ($active === 'oui') {
        //             $query->where('users.active', true);
        //         } elseif ($active === 'non') {
        //             $query->where('users.active', false);
        //         }
        //     }
        
        //     // Inclure les informations de l'utilisateur si le paramètre 'include' est 'user'
        //     if ($includeUser) {
        //         $query->addSelect('users.*');
        //     }
        
        //     // Utilisation du Lazy Query Builder
        //     $clients = $query->cursor(); 
        
        //     // Traitement des clients
        //     $data = [];
        //     foreach ($clients as $client) {
        //         $clientData = [
        //             'id' => $client->id,
        //             'surnom' => $client->surnom,
        //             'adresse' => $client->adresse,
        //             'telephone' => $client->telephone,
        //         ];
        
        //         if ($includeUser && $client->user_id) {
        //             $clientData['user'] = [
        //                 'id' => $client->user_id,
        //                 'active' => $client->active,
        //                 // Inclure d'autres informations sur l'utilisateur si nécessaire
        //             ];
        //         }
        
        //         $data[] = $clientData;
        //     }
        
        //     // Retourner une réponse JSON
        //     return $this->sendResponse(200, $data, 'Liste des clients récupérée avec succès.');
        // }

        public function findByTelephone(Request $request) {
            $telephone=$request->input('telephone'); 
            return  ClientServiceFacade::findByTelephone($telephone);
        }
      /**
 * @OA\Post(
 *     path="/api/clients/telephone",
 *     summary="Rechercher un client par numéro de téléphone",
 *     tags={"Clients"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"telephone"},
 *             @OA\Property(property="telephone", type="string", example="123456789")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Client trouvé",
 *         @OA\JsonContent(ref="#/components/schemas/Client")
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Client non trouvé",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="integer", example=404),
 *             @OA\Property(property="message", type="string", example="Pas Client")
 *         )
 *     ),
 *     security={{"BearerToken": {}}}
 * )
 */  
        //find client by phone
        // public function findByTelephone(Request $request)
        // {
        //     // Valider la requête pour s'assurer qu'un numéro de téléphone est fourni
        //     $validatedData = $request->validate([
        //         'telephone' => 'required|string|max:9',
        //     ]);

        //     // Rechercher le client par son numéro de téléphone
        //     $client = Client::where('telephone', $validatedData['telephone'])->first();

        //     // Vérifier si un client a été trouvé
        //     if (!$client) {
        //         return response()->json([
        //             'status' => 404,
        //             'data' => 'null',
        //             'message' => 'Pas Client'
        //         ], 404);
        //     }

        //     // Retourner le client trouvé
        //     return response()->json([
        //         'status' => 200,
        //         'data' => $client,
        //         'message' => 'liste client'
        //     ], 200);
        // }

        
    //get clients by phone
    public function getByPhone($phone){ 
        return Client::where('telephone', $phone)->first();
    }
/**
 * @OA\Get(
 *     path="/api/clients/{id}",
 *     summary="Récupérer un client spécifique",
 *     tags={"Clients"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Parameter(
 *         name="include",
 *         in="query",
 *         required=false,
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Client récupéré avec succès",
 *         @OA\JsonContent(ref="#/components/schemas/Client")
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Client non trouvé",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Client not found")
 *         )
 *     ),
 *     security={{"BearerToken": {}}}
 * )
 */
     // Affiche un utilisateur spécifique par son ID
     public function show(Request $request, $id)
     {
         // Récupération du paramètre pour inclure les informations de l'utilisateur
         $includeUser = $request->query('include') === 'user';
     
         // Récupération du client avec les informations de l'utilisateur si demandé
         $client = Client::with($includeUser ? 'user' : [])->find($id);
     
         // Vérifier si le client existe
         if (!$client) {
             return response()->json(['message' => 'Client not found'], 404);
         }
     
         // Si l'utilisateur existe et qu'il a une photo, la convertir en base64
         if ($includeUser && $client->user && $client->user->photo) {
        }
        
        $client->user->photo = ImageUploadFacade::getBase64($client->user->photo);
        dd($client->user->photo);
         // Retourner les données du client
         return response()->json($client);
     }
     

    public function showClientWithUser($id)
    {
        try {
            // Récupérer le client avec les informations de l'utilisateur associé
            $client = Client::with('user')->find($id);

            // Vérifier si le client existe
            if (!$client) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Client not found'
                ], 404);
            }

            // Retourner les données du client avec l'utilisateur associé
            return response()->json([
                'status' => 200,
                'client' => $client
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Erreur lors de la récupération du client: ' . $e->getMessage()
            ], 500);
        }
    }
/**
 * @OA\Post(
 *     path="/clients",
 *     summary="Créer un client",
 *     tags={"Clients"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"surnom", "adresse", "telephone"},
 *             @OA\Property(property="surnom", type="string", example="ClientSurnom"),
 *             @OA\Property(property="adresse", type="string", example="ClientAdresse"),
 *             @OA\Property(property="telephone", type="string", example="784316538"),
 *             @OA\Property(property="user", type="object", description="Détails de l'utilisateur associé"),
 *             @OA\Property(property="photo", type="string", format="binary")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Client créé avec succès",
 *         @OA\JsonContent(ref="#/components/schemas/Client")
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Erreur lors de la création du client",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="integer", example=500),
 *             @OA\Property(property="message", type="string", example="Erreur lors de la création du client")
 *         )
 *     ),
 *     security={{"BearerToken": {}}}
 * )
 */
     // Crée un nouveau clientpublic function store(StoreRequest $request)
    //  public function store(StoreRequest $request)
    //  {
    //      try {
    //          // Début de la transaction
    //          DB::beginTransaction();
             
    //          // Création de l'utilisateur, si fourni
    //          $user = null;
    //          if ($request->has('user')) {
    //              $userData = $request->input('user');
    //              $userData['password'] = bcrypt($userData['password']);
    //              $user = Users::create($userData);
    //          }
             
    //          // Création du client
    //          $clientData = $request->validated();
    //          $clientData['user_id'] = $user ? $user->id : null;
     
    //          // Gestion du fichier de la photo
    //          if ($request->hasFile('photo')) {
    //              $filePath = $request->file('photo')->store('photos', 'public');
    //              $userData['photo'] = $filePath;
    //          }
     
    //          $client = Client::create($clientData);
     
    //          // Associer le client à l'utilisateur, si existant
    //          if ($user) {
    //              $client->user()->associate($user);
    //          }
     
    //          $client->save();
     
    //          // Validation de la transaction
    //          DB::commit();
     
    //          return $this->sendResponse(200, new ClientResource($client), 'Client créé avec succès.');
    //      } catch (\Exception $e) {
    //          // En cas d'erreur, annuler la transaction
    //          DB::rollBack();
     
    //          return $this->sendResponse(500, null, 'Erreur lors de la création du client: ' . $e->getMessage());
    //      }
    //  }
       public function store(StoreRequest $request){
        
         // Récupération des données du client et du user
         $clientData = request()->validate([
            'surnom' =>'required|string',
             'adresse' =>'required|string',
             'telephone' =>'required|string|max:9',
             'user' => 'array',
             'user.email' =>'required|email',
             'user.password' => 'nullable|string|min:8|confirmed',
             'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
         ]);
      
         $userData = $clientData['user'];
         $userData['password'] =  bcrypt($userData['password']);
      
         // Création du user
         $user = User::create($userData);
         $user->save();
         // Création du client
         $clientData['user_id'] = $user->id;

         // Gestion du fichier de la photo
         if($request->hasFile('photo')){
             $photo = $request->file('photo');
             $photoName = time(). '.'. $photo->getClientOriginalExtension();
             $photo->move(public_path('images'), $photoName);
             $clientData['photo'] = $photoName;
         }

         $client = ClientServiceFacade::create($clientData);

         return response()->json([
            'status' => 200,
             'data' => $client,
             'message' => 'Client created successfully'
         ], 200);
       }  

    }