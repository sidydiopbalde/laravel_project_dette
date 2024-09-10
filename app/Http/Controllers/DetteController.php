<?php

namespace App\Http\Controllers;

use App\Models\Dette;
use Illuminate\Http\Request;
use App\Services\DetteService;
use App\Http\Requests\StoreDetteRequest;
use App\Facades\DetteServiceFacade;
use Illuminate\Support\Facades\DB;
class DetteController extends Controller
{
   
   /**
 * @OA\Post(
 *     path="/api/v1/dettes",
 *     summary="Créer une nouvelle demande de crédit",
 *     tags={"Dettes"},
 *     description="Créer une nouvelle demande de crédit pour un client spécifique",
 *     @OA\Parameter(
 *         name="clientId",
 *         in="path",
 *         required=true,
 *         description="ID du client pour lequel créer la demande de crédit",
 *         @OA\Schema(type="integer")
 *     ),
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
    public function index($clientId)
    {
        // Récupérer les dettes du client par ID
        $dettes = Dette::where('client_id', $clientId)->get();
        return $dettes;
    }

/**
 * @OA\Post(
 *     path="/api/articles/clients/{clientId}/dettes",
 *     summary="Créer une nouvelle demande de crédit",
 *     tags={"Dettes"},
 *     description="Créer une nouvelle demande de crédit pour un client spécifique",
 *     @OA\Parameter(
 *         name="clientId",
 *         in="path",
 *         required=true,
 *         description="ID du client pour lequel créer la demande de crédit",
 *         @OA\Schema(type="integer")
 *     ),
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
    public function listDettes(Request $request, $id)
    {
        try {
            // Utiliser le service pour récupérer les dettes du client
            $data = DetteServiceFacade::getClientDettes($id);

            return response()->json([
                'success' => true,
                'message' => 'Informations du client et ses dettes',
                'data' => $data,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 404);
        }
    }  
   
    public function store(StoreDetteRequest $request)
    {
        $validated = $request->validated();
        $debt = DetteServiceFacade::createDette($validated);
         return $debt;
    }
    //recupere les dettes non soldé ou soldé
    public function scope_Dette_by_statut(Request $request)
    {
        $statut = $request->query('statut', 'Solde'); // Par défaut 'Solde'
        // Utiliser le scope pour filtrer les dettes selon leur statut calculé et charger les relations
        $dettes = Dette::with(['client', 'paiements', 'articles'])->statut($statut)->get();
        return  $dettes;
    }

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
    public function listArticles($id)
    {
        try {
            // Appel au service pour récupérer les articles de la dette
            $articles = DetteServiceFacade::getArticlesByDetteId($id);
            if($articles){
                return response()->json([
                    'success' => true,
                    'message' => 'Articles récupérés avec succès',
                    'data' => $articles
                ], 200);
            }
            return response()->json([
               'success' => false,
               'message' => 'Aucun article trouvé pour cette dette',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des articles',
                'error' => $e->getMessage()
            ], 404);
        }
    }
    public function listPaiements($id)
    {
        try {
            // Appel au service pour récupérer les paiements de la dette
            $paiements = DetteServiceFacade::getPaiementsByDetteId($id);

            return response()->json([
                'success' => true,
                'message' => 'Paiements récupérés avec succès',
                'data' => $paiements
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des paiements',
                'error' => $e->getMessage()
            ], 404);
        }
    }
    //ajouter articles dans une dette
    // public function addArticles(Request $request, $id)
    // {
    //     $validatedData = $request->validate([
    //         'articles.*.articleId' => 'required|exists:articles,id',
    //         'articles.*.qte' => 'required|integer|min:1',
    //         'articles.*.prix_unitaire' => 'required|numeric',
    //     ]);

    //     try {
    //         $dette = $this->detteService->addArticlesToDette($validatedData['articles'], $id);
    //         return response()->json([
    //             'message' => 'Articles ajoutés avec succès',
    //             'data' => $dette
    //         ], 200);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Erreur lors de l\'ajout des articles.',
    //             'error' => $e->getMessage()
    //         ], 400);
    //     }
    // }
}
