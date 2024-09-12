<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserControllers;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\DetteController;
use App\Http\Controllers\FirebaseController;
use App\Http\Controllers\MongoTestController;
use App\Http\Controllers\PaiementController;
use App\Http\Controllers\SmsController;
use App\Http\Controllers\MongoDBTestController;
use App\Http\Controllers\SmsbipController;
use App\Jobs\ArchiveSoldeJob;

Route::post('/v1/register', [UserController::class, 'storeUserClientExist']);
// Route::middleware(['auth:api', 'can:accessAdminRoutes,App\Models\User','json.response'])->post('/v1/register', [UserController::class, 'storeUserClientExist'])->name('users.registerClient');

Route::prefix('v1')->middleware('json.response')->group(function () {
    // Authentification et utilisateur
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    
    Route::middleware(['auth:api'])->post('/registerUser', [UserController::class, 'store']);
    // Routes protégées par authentification et permission d'admin
    Route::prefix('users')->middleware(['auth:api', 'can:accessAdminRoutes,App\Models\User'])->group(function () {
        Route::get('/', [UserController::class, 'index']); //A revoir
        Route::post('/', [UserController::class, 'store']);
        Route::get('/{id}', [UserController::class, 'show']);
        Route::put('/{id}', [UserController::class, 'update']);
        Route::patch('/{id}', [UserController::class, 'update']);
        Route::delete('/{id}', [UserController::class, 'destroy']);
    });

    // Routes pour les clients protégées par authentification
    Route::prefix('clients')->middleware(['auth:api', 'can:access,App\Models\Article'])->group(function () {
        Route::get('/', [ClientController::class, 'index'])->name('clients.index');
        Route::get('{id}/dettes', [DetteController::class, 'index'])->name('clients.dettes');
        Route::get('{id}/users', [ClientController::class, 'showClientWithUser'])->name('clients.show');
        Route::post('/', [ClientController::class, 'store'])->name('clients.store');
        Route::put('{id}', [ClientController::class, 'update'])->name('clients.update');
        Route::patch('{id}', [ClientController::class, 'update'])->name('clients.partial_update');
        Route::delete('{id}', [ClientController::class, 'destroy'])->name('clients.destroy');
        Route::post('/telephone', [ClientController::class, 'findByTelephone'])->name('clients.telephone');
        Route::post('/filter', [ClientController::class, 'filter'])->name('clients.filter');
    });
    // Route::get('clients/{id}/users', [ClientController::class, 'show']);

    // Routes pour les articles protégées par authentification
    Route::prefix('articles')->middleware(['auth:api', 'can:access,App\Models\Article'])->group(function () {
        Route::get('/', [ArticleController::class, 'index'])->name('articles.index');
        Route::get('{id}', [ArticleController::class, 'show'])->name('articles.show');
        Route::post('/libelle', [ArticleController::class, 'index']);
        Route::post('/', [ArticleController::class, 'store'])->name('articles.store');
        Route::patch('stock', [ArticleController::class, 'updateQuantities']);
        Route::patch('{id}', [ArticleController::class, 'update'])->name('articles.update');
        Route::delete('{id}', [ArticleController::class, 'destroy'])->name('articles.destroy');
    });
    // Routes pour les dettes et paiements protégées par authentification
    Route::prefix('dettes')->middleware(['auth:api', 'can:accessClientOrBoutiquierRoutes,App\Models\User'])->group(function () {
        Route::post('/{id}/articles', [DetteController::class, 'listArticles']);
        Route::post('{id}/paiements', [DetteController::class, 'listPaiements']);
    });
    // Routes pour les dettes protégées par authentification
    Route::prefix('dettes')->middleware(['auth:api', 'can:access,App\Models\Article'])->group(function () {
        Route::post('/', [DetteController::class, 'store']);
        Route::get('/', [DetteController::class, 'showStatut']);//Solde ou NonSolde
         Route::get('/{id}', [DetteController::class, 'show']);
        Route::put('/{id}', [DetteController::class, 'update']);
        Route::delete('/{id}', [DetteController::class, 'delete']);
        Route::get('/client/{clientId}', [DetteController::class, 'getDebtsByClient']);
    });

    // Route::prefix('paiements')->middleware(['auth:api', 'can:access,App\Models\Paiement'])->group(function () {
        Route::prefix('paiements')->middleware(['auth:api', 'can:access,App\Models\Article'])->group(function () {
            Route::post('/{detteId}', [PaiementController::class, 'effectuerPaiement']);
        
    });
});

// Route pour obtenir l'utilisateur authentifié
Route::middleware(['auth:api', 'CentralizeResponse'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/v1/test-twilio', function () {
    return [
        'TWILIO_SID' => env('TWILIO_SID'),
        'TWILIO_AUTH_TOKEN' => env('TWILIO_AUTH_TOKEN'),
        'TWILIO_PHONE_NUMBER' => env('TWILIO_PHONE_NUMBER'),
    ];
});

Route::post('/v1/test-sms', [SmsController::class, 'sendSms']);
Route::post('/v1/test-mongo', [MongoDBTestController::class, 'testConnection']);
// Route::get('/archive-dettes', [MongoTestController::class, 'archiveSoldedDettes']);
Route::get('/v1/test-archive', function () {
    ArchiveSoldeJob::dispatch();
    return 'Job dispatched!';
});

// Route pour archiver les dettes soldées dans Firebase
Route::get('/firebase', [FirebaseController::class, 'index']);
Route::post('/firebase', [FirebaseController::class, 'store']);
Route::post('/firebaseSave', [FirebaseController::class, 'archiveDettes']);

//infoBip sendSms
Route::get('/v1/send-info_bip', [SmsbipController::class, 'sendSms']);