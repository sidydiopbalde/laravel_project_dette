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
    // protected $debtService;

    // public function __construct(DetteService $debtService)
    // {
    //     $this->debtService = $debtService;
    //}
    /**
     * @OA\Schema(
     *     schema="Dette",
     *     type="object",
     *     title="Dette",
     *     required={"client_id", "montant","montant_due", "date"},
     *     @OA\Property(property="id", type="integer", example=1),
     *     @OA\Property(property="client_id", type="integer", example=1),
     *     @OA\Property(property="montant", type="number", format="float", example=150.75),
     *     @OA\Property(property="montant_due", type="number", format="float", example=150.75),
     *     @OA\Property(property="date", type="string", format="date", example="2023-09-01"),
     *     @OA\Property(property="created_at", type="string", format="date-time", example="2023-09-01T00:00:00Z"),
     *     @OA\Property(property="updated_at", type="string", format="date-time", example="2023-09-01T00:00:00Z")
     * )
     */

    /**
     * @OA\Get(
     *     path="/api/articles/clients/{clientId}/dettes",
     *     summary="Récupérer les dettes d'un client spécifique",
     *     tags={"Dettes"},
     *     @OA\Parameter(
     *         name="clientId",
     *         in="path",
     *         required=true,
     *         description="ID du client pour lequel récupérer les dettes",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Liste des dettes du client",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Dette")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Aucune dette trouvée pour ce client."
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
     * description
     *     @OA\Parameter(
     *         name="clientId",
     *         in="path",
     *         required=true,
     *         description="ID du client pour lequel créer la demande de crédit",
     * description="
     * 
     * 
     * 
     * 
     * **/
     
    // Ajouter une nouvelle demande de crédit
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

}
