<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserControllers;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\DetteController;


Route::middleware(['auth:api', 'json.response'])->post('/v1/register', [UserController::class, 'storeUserClientExist'])->name('users.registerClient');

Route::prefix('v1')->middleware('json.response')->group(function () {
    // Authentification et utilisateur
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/registerUser', [UserController::class, 'store']);

    // Routes protégées par authentification et permission d'admin
    Route::prefix('users')->middleware(['auth:api', 'can:accessAdminRoutes,App\Models\User'])->group(function () {
        Route::get('/', [UserController::class, 'index']);
        Route::post('/', [UserController::class, 'store']);
        Route::get('/{id}', [UserController::class, 'show']);
        Route::put('/{id}', [UserController::class, 'update']);
        Route::patch('/{id}', [UserController::class, 'update']);
        Route::delete('/{id}', [UserController::class, 'destroy']);
    });

    // Routes pour les clients protégées par authentification
    Route::prefix('clients')->middleware(['auth:api', 'can:access,App\Models\Article'])->group(function () {
        Route::get('/', [ClientController::class, 'index'])->name('clients.index');
        Route::get('{id}', [ClientController::class, 'show'])->name('clients.show');
        Route::post('/', [ClientController::class, 'store'])->name('clients.store');
        Route::put('{id}', [ClientController::class, 'update'])->name('clients.update');
        Route::patch('{id}', [ClientController::class, 'update'])->name('clients.partial_update');
        Route::delete('{id}', [ClientController::class, 'destroy'])->name('clients.destroy');
        Route::post('/telephone', [ClientController::class, 'findByTelephone'])->name('clients.telephone');
        Route::post('/filter', [ClientController::class, 'filter'])->name('clients.filter');
    });

    // Routes pour les articles protégées par authentification
    Route::prefix('articles')->middleware(['auth:api', 'can:access,App\Models\Article'])->group(function () {
        Route::get('/', [ArticleController::class, 'index'])->name('articles.index');
        Route::get('{id}', [ArticleController::class, 'show'])->name('articles.show');
        Route::post('/', [ArticleController::class, 'store'])->name('articles.store');
        Route::patch('stock', [ArticleController::class, 'updateQuantities']);
        Route::patch('{id}', [ArticleController::class, 'update'])->name('articles.update');
        Route::delete('{id}', [ArticleController::class, 'destroy'])->name('articles.destroy');
    });

    // Routes pour les dettes protégées par authentification
    Route::prefix('dettes')->middleware(['auth:api', 'can:access,App\Models\Article'])->group(function () {
        Route::post('/', [DetteController::class, 'store']);
        Route::get('/', [DetteController::class, 'scope_Dette_by_statut']);
        Route::put('/{id}', [DetteController::class, 'update']);
        Route::delete('/{id}', [DetteController::class, 'delete']);
        Route::get('/client/{clientId}', [DetteController::class, 'getDebtsByClient']);
    });
});

// Route pour obtenir l'utilisateur authentifié
Route::middleware(['auth:api', 'CentralizeResponse'])->get('/user', function (Request $request) {
    return $request->user();
});

// Route::prefix('v1')->group(function () {
//     Route::post('/login', [AuthController::class, 'login'])->name('login'); // Route pour la connexion
//     Route::post('/registerUser', [UserController::class, 'store']); // Créer un nouvel utilisateur
    
    
//     Route::middleware(['auth:api','can:accessAdminRoutes,App\Models\User'])->group(function () {
//         Route::get('/users', [UserController::class, 'index']); // Récupérer tous les utilisateurs
//         Route::get('/users/{id}', [UserController::class, 'show']); // Récupérer un utilisateur spécifique
//         Route::put('/users/{id}', [UserController::class, 'update']); // Mettre à jour un utilisateur existant
//         Route::patch('/users/{id}', [UserController::class, 'update']); // Mettre à jour partiellement un utilisateur existant
//         Route::delete('/users/{id}', [UserController::class, 'destroy']); // Supprimer un utilisateur
//     });
// });

// Route::middleware(['auth:api', 'can:access,App\Models\Article'])->post('v1/register', [UserController::class, 'storeUserClientExist'])->name('register'); 
// // Routes pour les clients, protégées par Passport
// Route::prefix('clients')->middleware(['auth:api', 'can:access,App\Models\Article'])->group(function () {
//     Route::get('/', [ClientController::class, 'index'])->name('clients.index'); // Récupérer tous les clients
//     Route::get('{id}', [ClientController::class, 'show'])->name('clients.show'); // Récupérer un client spécifique
//     Route::post('{id}/user', [ClientController::class, 'showClientWithUser'])->name('clients.show'); // Récupérer un client spécifique
//     Route::post('/', [ClientController::class, 'store'])->name('clients.store'); // Créer un client
//     Route::put('{id}', [ClientController::class, 'update'])->name('clients.update'); // Mettre à jour un client existant
//     Route::patch('{id}', [ClientController::class, 'update'])->name('clients.partial_update'); // Mettre à jour partiellement un client existant
//     Route::delete('{id}', [ClientController::class, 'destroy'])->name('clients.destroy'); // Supprimer un client
//     Route::post('/telephone', [ClientController::class, 'findByTelephone'])->name('clients.telephone'); // Rechercher un client par nom
//     Route::post('/filter', [ClientController::class, 'filter'])->name('clients.filter'); // Filtrer les clients selon des critères  
// });


// // Routes pour les articles, protégées par Passport
// Route::prefix('articles')->middleware('auth:api')->middleware(['auth:api', 'can:access,App\Models\Article'])->group(function () {
//     Route::get('/', [ArticleController::class, 'index'])->name('articles.index'); // Afficher la liste des articles
//     Route::get('{id}', [ArticleController::class, 'show'])->name('articles.show'); // Afficher un article spécifique
//     Route::get('/clients/{clientId}/dettes', [DetteController::class, 'index']);
//     Route::post('/libelle', [ArticleController::class, 'index'])->name('articles.show'); // Afficher un article spécifique
//     Route::get('create', [ArticleController::class, 'create'])->name('articles.create'); // Afficher le formulaire de création d'un article
//     Route::post('/', [ArticleController::class, 'store'])->name('articles.store'); // Enregistrer un nouvel article
//     Route::get('{id}/edit', [ArticleController::class, 'edit'])->name('articles.edit'); // Afficher le formulaire d'édition d'un article
//     Route::patch('stock', [ArticleController::class, 'updateQuantities']); // Mettre à jour la quantité des articles
//     Route::patch('{id}', [ArticleController::class, 'update'])->name('articles.update'); // Mettre à jour un article spécifique
//     Route::delete('{id}', [ArticleController::class, 'destroy'])->name('articles.destroy'); // Supprimer un article spécifique
//     Route::post('{id}', [ArticleController::class, 'destroy'])->name('articles.destroy'); // Supprimer un article spécifique

// });
// //Routes pour les clients
// // Route pour obtenir l'utilisateur authentifié
// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });
// Route::prefix('v1')->middleware('auth:api')->middleware(['auth:api', 'can:access,App\Models\Article'])->group(function () {
// Route::post('/dettes', [DetteController::class, 'store']);
// Route::get('/dettes', [DetteController::class, 'scope_Dette_by_statut']);
// Route::put('/dettes/{id}', [DetteController::class, 'update']);
// Route::delete('/dettes/{id}', [DetteController::class, 'delete']);
// Route::get('/client/{clientId}/dettes', [DetteController::class, 'getDebtsByClient']);

// });