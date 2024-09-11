<?php
namespace App\Http\Controllers;

use App\Services\PaiementService;
use Illuminate\Http\Request;

class PaiementController extends Controller
{
    protected $paiementService;

    public function __construct(PaiementService $paiementService)
    {
        $this->paiementService = $paiementService;
    }
  /**
     * @OA\Post(
     *     path="/api/v1/dettes/{detteId}/paiements",
     *     summary="Effectuer un paiement sur une dette",
     *     tags={"Paiements"},
     *     description="Permet d'effectuer un paiement sur une dette spécifique.",
     *     @OA\Parameter(
     *         name="detteId",
     *         in="path",
     *         required=true,
     *         description="ID de la dette sur laquelle le paiement sera effectué",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"montant"},
     *             @OA\Property(property="montant", type="number", format="float", example=1000.00)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Paiement effectué avec succès",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Paiement effectué avec succès"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="dette_id", type="integer", example=1),
     *                 @OA\Property(property="montant", type="number", format="float", example=1000.00),
     *                 @OA\Property(property="date", type="string", format="date-time", example="2023-09-10T10:00:00Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Erreur lors du paiement",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Erreur lors du paiement"),
     *             @OA\Property(property="error", type="string", example="Montant trop élevé")
     *         )
     *     )
     * )
     */
    public function effectuerPaiement(Request $request, $detteId)
    {
        $validatedData = $request->validate([
            'montant' => 'required|numeric|min:1',
        ]);

        try {
            // Effectuer le paiement via le service
            $paiement = $this->paiementService->effectuerPaiement([
                'dette_id' => $detteId,
                'montant' => $validatedData['montant'],
            ]);

            return response()->json([
                'message' => 'Paiement effectué avec succès',
                'data' => $paiement,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erreur lors du paiement',
                'error' => $e->getMessage(),
            ], 400);
        }
    }
}
