<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ArticleController;

Route::prefix('v1')->group(function () {
    Route::post('/login', [AuthController::class, 'login'])->name('login'); // Route pour la connexion
    Route::post('/register', [UserController::class, 'store']); // Créer un nouvel utilisateur
    

    // Route::middleware('auth:api')->group(function () {
         Route::get('/users', [UserController::class, 'index']); // Récupérer tous les utilisateurs
        Route::get('{id}', [UserController::class, 'show']); // Récupérer un utilisateur spécifique
        Route::put('{id}', [UserController::class, 'update']); // Mettre à jour un utilisateur existant
        Route::patch('{id}', [UserController::class, 'update']); // Mettre à jour partiellement un utilisateur existant
        Route::delete('{id}', [UserController::class, 'destroy']); // Supprimer un utilisateur
        //  });
    });

// Routes pour les clients, protégées par Passport
Route::prefix('clients')->middleware('auth:api')->group(function () {
    Route::get('/withUser', [ClientController::class, 'index'])->name('clients.index'); // Récupérer tous les clients
    Route::get('{id}', [ClientController::class, 'show'])->name('clients.show'); // Récupérer un client spécifique
    Route::post('/', [ClientController::class, 'store'])->name('clients.store'); // Créer un client
    Route::put('{id}', [ClientController::class, 'update'])->name('clients.update'); // Mettre à jour un client existant
    Route::patch('{id}', [ClientController::class, 'update'])->name('clients.partial_update'); // Mettre à jour partiellement un client existant
    Route::delete('{id}', [ClientController::class, 'destroy'])->name('clients.destroy'); // Supprimer un client
    Route::post('/search', [ClientController::class, 'search'])->name('clients.search'); // Rechercher un client par nom
    Route::post('/filter', [ClientController::class, 'filter'])->name('clients.filter'); // Filtrer les clients selon des critères  
});

// Routes pour les articles, protégées par Passport
Route::prefix('articles')->middleware('auth:api')->group(function () {
    Route::get('/', [ArticleController::class, 'index'])->name('articles.index'); // Afficher la liste des articles
    Route::get('/{id}', [ArticleController::class, 'show'])->name('articles.show'); // Afficher un article spécifique
    Route::get('create', [ArticleController::class, 'create'])->name('articles.create'); // Afficher le formulaire de création d'un article
    Route::post('/', [ArticleController::class, 'store'])->name('articles.store'); // Enregistrer un nouvel article
    Route::get('{id}/edit', [ArticleController::class, 'edit'])->name('articles.edit'); // Afficher le formulaire d'édition d'un article
    Route::patch('{id}', [ArticleController::class, 'update'])->name('articles.update'); // Mettre à jour un article spécifique
    Route::patch('stock', [ArticleController::class, 'updateQuantities'])->name('articles.updateStock'); // Mettre à jour la quantité des articles
    Route::delete('{id}', [ArticleController::class, 'destroy'])->name('articles.destroy'); // Supprimer un article spécifique
});

// Route pour obtenir l'utilisateur authentifié
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

// Route pour afficher tous les utilisateurs
// Route::get('/users',function (Request $request) {
//     return response()->json("users", 200);
// });