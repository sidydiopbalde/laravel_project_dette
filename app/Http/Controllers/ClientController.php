<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use App\Http\Requests\StoreRequest;
use App\Traits\ApiResponseTrait;
use App\Facades\ClientServiceFacade;

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
    public function index(Request $request)
    {
        // Récupère les clients filtrés automatiquement via le scope global
        $clients = Client::all();  // Le scope global sera appliqué ici
        return $clients;
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
  
 public function findByTelephone(Request $request) {

    $telephone = $request->input('telephone');
    return  ClientServiceFacade::findByTelephone($telephone);
    
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

     
    public function show(Request $request, $id)
    {
            $includeUser = $request->query('include') === 'user';
            $client = ClientServiceFacade::getClientById($id, $includeUser);
            return $client;
       
    }

  
    public function showClientWithUser($id)
    {
            $client = Client::with('user')->find($id);
             return $client;      
    }
/**
 * @OA\Post(
 *     path="/api/clients",
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

 public function store(StoreRequest $request)
 {
         $client =ClientServiceFacade::createClient($request);
         return $client;
 }
   

 }