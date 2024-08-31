<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use App\Http\Requests\StoreArticleRequest;
use App\Http\Requests\UpdateArticleRequest;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\ArticleResource;
use App\Traits\ApiResponseTrait;
class ArticleController extends Controller
{
    use ApiResponseTrait;
    /**
     * Display a listing of the resource.
     */
        public function index()
        {
            $articles = Article::all(); // Récupère tous les articles
            return response()->json($articles); // Retourne la liste des articles en JSON
        }



   
    
   

    public function store(StoreArticleRequest $request)
    {
        // Les données sont déjà validées par StoreArticleRequest
        $validated = $request->validated();
        
        // Création de l'article
        $article = Article::create($validated);
    
        // return $this->sendResponse(201, ArticleResource::collection($article), 'L\'article ajouté avec succès.');
        return response()->json($article, 201);
    } // Retourne l'article créé en JSON avec un code de statut 201
    
    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $article = Article::find($id);
        if (!$article) {
            return response()->json(['message' => 'article not found'], 404);
        }
        return response()->json($article, 201); 
    }
    
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Article $article)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    // public function update(Request $request, Article $article)
    // {
    //     // Validation des données
    //     $validated = $request->validate([
    //         'libelle' => 'required|string|max:255',
    //         'qte' => 'required|string',
    //         'prix_unitaire' => 'required|string',
    //     ]);
    
    //     // Mise à jour de l'article
    //     $article->update($validated);
    
    //     return response()->json($article); // Retourne l'article mis à jour en JSON
    // }

//update Article by id
    public function update(UpdateArticleRequest $request, $id)
    {
        $article = Article::find($id);
    //    dd($article);
        if (!$article) {
            return response()->json(['error' => 'Article non trouvé'], 404);
        }
        
        try {
            // Mettre à jour l'article augmenter le stock 
            $article->qte += $request->input('qte');  // Augmenter la quantité en stock par la quantité fournie dans la requête
            
            $article->save([
                 'qte' => $article->qte,  
            ]);
          
     
            return response()->json(['article' => $article, 'message' => 'Article mis à jour avec succès']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur lors de la mise à jour de l\'article'], 500);
        }
    }

    //update un ou plusieurs articles
    public function updateQuantities(Request $request)
    {
        // Validation des données
        $validated = $request->validate([
            'articles' => 'required|array',
            'articles.*.id' => 'required|integer|exists:articles,id',
            'articles.*.qte' => 'required|integer|min:1',
        ]);

        // Parcourir chaque article à mettre à jour
        foreach ($validated['articles'] as $articleData) {
            $article = Article::find($articleData['id']);

            // Ajouter la nouvelle quantité à la quantité existante
            $article->qte += $articleData['qte'];

            // Sauvegarder l'article mis à jour
            $article->save();
        }

        return response()->json(['message' => 'Les articles ont été mis à jour avec succès.']);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Article $article)
    {
        $article->delete(); // Effectue la suppression logique

        return response()->json(['message' => 'Article supprimé avec succès']);
    }
    
}
