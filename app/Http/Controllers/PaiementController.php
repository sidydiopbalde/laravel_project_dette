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
