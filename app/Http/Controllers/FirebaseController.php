<?php

namespace App\Http\Controllers;

use App\Models\Dette;
use App\Services\FirebaseServiceInterface;
use Illuminate\Http\Request;

class FirebaseController extends Controller
{
    protected $firebase;
    protected $firebasesave;
   

    public function __construct(FirebaseServiceInterface $firebase)
    {
        // $this->firebase = $firebase->getDatabase();
        // $this->firebasesave = $firebasesave->archiveDettes();

    }

    // public function index()
    // {
    //     $reference = $this->firebase->getReference('test'); // Specify your data path
    //     $snapshot = $reference->getSnapshot();
    //     $value = $snapshot->getValue();

    //     return response()->json($value); // Return the data as JSON
    // }

    // public function store(Request $request)
    // {
    //     $newData = $this->firebase->getReference('test2')->push($request->all());
    //     return response()->json($newData->getValue());
    // }
    public function archiveDettes()
    {
        // Récupérer les dettes soldées
        // $dettesSoldes = Dette::whereHas('paiements', function ($query) {
        //     $query->havingRaw('SUM(montant) >= dettes.montant');
        // })->get();
        $dettes = Dette::statut('Solde')->get();
        // dd($dettes);
        // Archiver chaque dette
        foreach ($dettes as $dette) {

            app(FirebaseServiceInterface::class)->archiveDette($dette);
            // $this->firebasesave->archiveDette($dette);
        }

        // Retourner une réponse de succès
        return response()->json([
            'message' => 'Dettes archivées avec succès dans Firebase.',
        ], 200);
    }
}