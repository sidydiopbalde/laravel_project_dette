<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Clients;
use App\Models\Users;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\StoreRequest;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\QueryBuilder;
use App\Http\Requests\StoreUserRequest;
use App\Http\Resources\ClientResource;
use App\Traits\ApiResponseTrait;
class ClientController extends Controller
{
    use ApiResponseTrait;
    //get clients_user with pagination
        public function all(Request $request){
            $size = $request->query('size', 2);
            return Clients::with('user')->paginate($size);
        }
    //get clients_user 
        public function getAll(Request $request){
         
            return Clients::with('user')->get();
        }

        //
    public function index(Request $request)
    {
        // Récupération des paramètres de requête
        $telephones = $request->query('telephone');
        $surnom = $request->query('surnom');
        $includeUser = $request->query('include') === 'user';
    
        // Construction de la requête
        $query = Clients::query();
    
        // Application des filtres si des numéros de téléphone ou un surnom sont fournis
        if ($telephones) {
            // Assurez-vous que $telephones est un tableau
            if (!is_array($telephones)) {
                $telephones = explode(',', $telephones);
            }
            // Filtrage par plusieurs numéros de téléphone
            $query->whereIn('telephone', $telephones);
        }
    
        if ($surnom) {
            // Filtrage par surnom
            $query->where('surnom', 'like', '%' . $surnom . '%');
        }
    
        // Inclure les informations de l'utilisateur si le paramètre 'include' est 'user'
        if ($includeUser) {
            $query->with('user');
        }
    
        // Utilisation du Lazy Query Builder
        $clients = $query->cursor(); // Récupération des données en mode paresseux
    
        // Traitement des clients
        $data = [];
        foreach ($clients as $client) {
            $clientData = [
                'id' => $client->id,
                'surnom' => $client->surnom,
                'adresse' => $client->adresse,
                'telephone' => $client->telephone,
                // Ajoutez d'autres champs si nécessaire
            ];
    
            if ($includeUser) {
                $clientData['user'] = $client->user; // Inclure les informations de l'utilisateur
            }
    
            $data[] = $clientData;
        }
    
        // Retourner une réponse JSON
        return $this->sendResponse(200, $data, 'Liste des clients récupérée avec succès.');
    }
    
     // Affiche un utilisateur spécifique par son ID
     public function show(Request $request, $id)
{
    // Récupération du paramètre pour inclure les informations de l'utilisateur
    $includeUser = $request->query('include') === 'user';

    // Récupération du client avec les informations de l'utilisateur si demandé
    $client = Clients::with($includeUser ? 'user' : [])
        ->find($id);

    // Vérifier si le client existe
    if (!$client) {
        return response()->json(['message' => 'Client not found'], 404);
    }

    // Retourner les données du client
    return response()->json($client);
}

     // Crée un nouveau clientpublic function store(StoreRequest $request)
     public function store(StoreRequest $request)
     {
         try {
             // Début de la transaction
             // DB::beginTransaction();
             
             // Création de l'utilisateur, si fourni
             $user = null;
             if ($request->has('user')) {
                 $userData = $request->input('user');
                 $userData['password'] = bcrypt($userData['password']);
                 $user = Users::create($userData);
                }
                
                // Création du client
                $clientData = $request->validated();
                //  dd($request->all());
                $clientData['user_id'] = $user ? $user->id : null;
                dd($clientData);
            $client = Clients::create($clientData);

            // Associer le client à l'utilisateur, si existant
            // if ($user) {
            //     $client->user()->associate($user);
            // }

            $client->save();

            // Validation de la transaction
            // DB::commit();

            return $this->sendResponse(200, new ClientResource($client), 'client crée avec succès.');

    } catch (\Exception $e) {
        // En cas d'erreur, annuler la transaction
        // DB::rollBack();

        // return response()->json([
        //     'message' => 'Erreur lors de la création du client!',
        //     'error' => $e->getMessage(),
        // ], 500);

        return $this->sendResponse(500, null, 'Erreur lors de la création du client!.');
    }
}

  

    }