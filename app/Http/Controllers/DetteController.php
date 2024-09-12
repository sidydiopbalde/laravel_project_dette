<?php

namespace App\Http\Controllers;

use App\Models\Dette;
use Illuminate\Http\Request;
use App\Services\DetteService;
use App\Http\Requests\StoreDetteRequest;
use App\Facades\DetteServiceFacade;
use App\Http\Resources\ClientResource;
use Illuminate\Support\Facades\DB;
/**
 * @OA\Schema(
 *     schema="Paiement",
 *     type="object",
 *     title="Paiement",
 *     required={"id", "dette_id", "montant", "date"},
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="dette_id", type="integer", example=1),
 *     @OA\Property(property="montant", type="number", format="float", example=150.75),
 *     @OA\Property(property="date", type="string", format="date", example="2023-09-01"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2023-09-01T00:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2023-09-01T00:00:00Z")
 * )
 */

/**
 * @OA\Schema(
 *     schema="Dette",
 *     type="object",
 *     title="Dette",
 *     required={"client_id", "montant"},
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="client_id", type="integer", example=1),
 *     @OA\Property(property="montant", type="number", format="float", example=1000.50),
 *     @OA\Property(property="date", type="string", format="date", example="2023-09-01"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2023-09-01T00:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2023-09-01T00:00:00Z")
 * )
 */

class DetteController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/clients/{clientId}/dettes",
     *     summary="Récupérer les dettes d'un client",
     *     tags={"Dettes"},
     *     description="Obtenir la liste des dettes pour un client spécifique",
     *     @OA\Parameter(
     *         name="clientId",
     *         in="path",
     *         required=true,
     *         description="ID du client",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Liste des dettes récupérée avec succès",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Dette"))
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Client non trouvé"
     *     )
     * )
     */
    public function index($clientId)
    {
        // Récupérer les dettes du client par ID
        $dettes = Dette::where('client_id', $clientId)->get();
        if ($dettes->isEmpty()) {
            return response()->json(['message' => 'Pas de dette pour le client'], 404);
        }
        return $dettes;
    }

    /**
     * @OA\Post(
     *     path="/api/v1/dettes",
     *     summary="Créer une nouvelle demande de crédit",
     *     tags={"Dettes"},
     *     description="Créer une nouvelle demande de crédit pour un client spécifique",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             required={"montant", "articles"},
     *             @OA\Property(property="montant", type="number", format="float", example=1000),
     *             @OA\Property(
     *                 property="articles",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="articleId", type="integer", example=1),
     *                     @OA\Property(property="qteVente", type="integer", example=5),
     *                     @OA\Property(property="prixVente", type="number", format="float", example=100)
     *                 )
     *             ),
     *             @OA\Property(property="paiement", type="object", 
     *                 @OA\Property(property="montant", type="number", format="float", example=500)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Dette créée avec succès",
     *         @OA\JsonContent(ref="#/components/schemas/Dette")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Requête invalide"
     *     )
     * )
     */
    public function store(StoreDetteRequest $request)
    {
        $validated = $request->validated();
        $debt = DetteServiceFacade::createDette($validated);
        return $debt;
    }

    /**
     * @OA\Get(
     *     path="/api/v1/dettes/{id}",
     *     summary="Récupérer une dette spécifique",
     *     tags={"Dettes"},
     *     description="Récupérer une dette en fonction de son ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la dette",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Dette trouvée",
     *         @OA\JsonContent(ref="#/components/schemas/Dette")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Dette non trouvée"
     *     )
     * )
     */
    public function show($id)
    {
        try {
            $dette = DetteServiceFacade::getDetteById($id);

            return response()->json([
                'success' => true,
                'message' => 'Dette trouvée',
                'data' => $dette
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Dette non trouvée',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/v1/dettes/{id}/articles",
     *     summary="Récupérer les articles d'une dette",
     *     tags={"Dettes"},
     *     description="Récupérer les articles associés à une dette spécifique",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la dette",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Articles récupérés avec succès",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Article"))
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Aucun article trouvé pour cette dette"
     *     )
     * )
     */
    public function listArticles($id)
    {
       
            // Appel au service pour récupérer les articles de la dette
            $articles = DetteServiceFacade::getArticlesByDetteId($id);
            if($articles){
                return response()->json([
                    'success' => true,
                    'message' => 'Articles récupérés avec succès',
                    'data' => $articles
                ], 200);
            }else{
                return response()->json([
                   'success' => false,
                   'message' => 'Aucun article trouvé pour cette dette',
                ], 404);
            }
          
       
    }

    /**
     * @OA\Get(
     *     path="/api/v1/dettes/{id}/paiements",
     *     summary="Récupérer les paiements d'une dette",
     *     tags={"Dettes"},
     *     description="Récupérer les paiements associés à une dette spécifique",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la dette",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Paiements récupérés avec succès",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Paiement"))
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Aucun paiement trouvé pour cette dette"
     *     )
     * )
     */
    public function listPaiements($id)
    {
      
            // Appel au service pour récupérer les paiements de la dette
            $paiements = DetteServiceFacade::getPaiementsByDetteId($id);
            if($paiements){
                return response()->json([
                   'success' => true,
                   'message' => 'Paiements récupérés avec succès',
                    'data' => $paiements
                ], 200);
            }
            else{
                return response()->json([
                   'success' => false,
                   'message' => 'Aucun paiement trouvé pour cette dette',
                ], 404);
            }
       
    }

   

    public function showStatut(Request $request)
    {
        // Récupérer le statut depuis la requête (par exemple, ?statut=Solde)
        $statut = $request->query('statut');
        
        // Vérifier si le statut est valide
        if (!in_array($statut, ['Solde', 'NonSolde'])) {
            return response()->json([
                'success' => false,
                'message' => 'Statut invalide',
            ], 400);
        }
    
        // Construire la requête
        $query = Dette::query();
        
        // Appliquer le scope statut si un statut est fourni
        if ($statut) {
            $query->statut($statut);
        }
    
        // Charger la relation client
        $dettes = $query->with('client')->get();
        
        if ($dettes->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Aucune dette trouvée avec ce statut',
            ], 404);
        }
    
        // Utiliser ClientResource pour transformer les données du client
        $dettes = $dettes->map(function ($dette) {
            return [
                'dette' => $dette,
                'client' => new ClientResource($dette->client),
            ];
        });
    
        return response()->json([
            'success' => true,
            'message' => 'Dettes récupérées avec succès',
            'data' => $dettes
        ], 200);
    }
    
    
}
