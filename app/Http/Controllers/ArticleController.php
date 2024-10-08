<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use App\Http\Requests\StoreArticleRequest;
use App\Http\Requests\UpdateArticleRequest;
use App\Http\Requests\UpdateArticlestockRequest;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\ArticleResource;
use App\Services\ArticleService;
use App\Traits\ApiResponseTrait;

/**
 * @OA\Schema(ArticleService
 *     schema="UpdateArticleRequest",
 *     type="object",
 *     required={"qte"},
 *     @OA\Property(property="qte", type="integer", example=10)
 * )
 */


/**
 * @OA\Schema(
 *     schema="Article",
 *     type="object",
 *     required={"libelle", "qte", "prix_unitaire"},
 *     @OA\Property(property="libelle", type="string", example="Article Name"),
 *     @OA\Property(property="qte", type="integer", example=100),
 *     @OA\Property(property="prix_unitaire", type="number", format="float", example=19.99),

 * )
 */

// /**
//  * @OA\Schema(
//  *     schema="UpdateArticlestockRequest",
//  *     type="object",
//  *     required={"articles"},
//  *     @OA\Property(
//  *         property="articles",
//  *         type="array",
//  *         @OA\Items(
//  *             type="object",
//  *             required={"id", "qte"},
//  *             @OA\Property(property="id", type="integer", example=1),
//  *             @OA\Property(property="qte", type="integer", example=10)
//  *         )
//  *     )
//  * )
//  */
class ArticleController extends Controller
{
    use ApiResponseTrait;

    protected $service;

    public function __construct(ArticleService $service)
    {
        $this->service = $service;
    }
    /**
     * @OA\Get(
     *     path="/api/v1/articles",
     *     summary="List all articles",
     *     tags={"Articles"},
     *     @OA\Parameter(name="libelle", in="query", description="Filter articles by libelle", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="disponible", in="query", description="Filter articles by availability", required=false, @OA\Schema(type="string", enum={"oui", "non"})),
     *     @OA\Response(
     *         response=200,
     *         description="List of articles",
     *         @OA\JsonContent(type="object", @OA\Property(property="status", type="integer"), @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Article")), @OA\Property(property="message", type="string"))
     *     ),
     *     @OA\Response(
     *         response=411,
     *         description="No articles found",
     *         @OA\JsonContent(type="object", @OA\Property(property="status", type="integer"), @OA\Property(property="data", type="null"), @OA\Property(property="message", type="string"))
     *     )
     * )
     */
    // public function index(Request $request)
    // {
    //     // Vérifiez l'accès avec la policy
    //     $this->authorize('access', Article::class);
    
    //     $libelle = $request->input('libelle');
    //     $disponible = $request->input('disponible');
    
    //     $query = Article::query();
    
    //     if ($libelle) {
    //         $query->where('libelle', 'like', '%' . $libelle . '%');
    //     }
    
    //     if ($disponible) {
    //         if ($disponible === 'oui') {
    //             $query->where('qte', '>', 0); // Article disponible si qte > 0
    //         } elseif ($disponible === 'non') {
    //             $query->where('qte', '=', 0); // Article non disponible si qte = 0
    //         }
    //     }
    
    //     $articles = $query->paginate(2);
    
    //     if ($articles->isEmpty()) {
    //         return response()->json([
    //             'status' => 411,
    //             'data' => null,
    //             'message' => 'Objet non trouvé.',
    //         ], 411);
    //     }
    
    //     return response()->json([
    //         'status' => 200,
    //         'data' => $articles,
    //         'message' => 'Liste des articles récupérée avec succès.',
    //     ]);
    // }
    

    // public function index()
    // {
    //     $articles = $this->service->all();
    //     return response()->json($articles);
    // }

   /*  public function index(Request $request)
    {
       $libelle = $request->input('libelle');
        // Appliquer le scope local pour filtrer les articles par libelle
        if ($libelle) {
            $articles = Article::libelle($libelle)->get();
        } else {
          
            $articles = $this->service->all();
        }

        return $articles;
    } */
        public function index(Request $request)
    {
        $libelle = $request->input('libelle');
        $disponible = $request->input('disponible', 'oui'); // Valeur par défaut : articles disponibles

        // Vérifier si on doit chercher les articles disponibles ou non disponibles
        $isAvailable = $disponible === 'oui';

        // Appliquer le scope pour filtrer par libelle et disponibilité
        $query = Article::disponible($isAvailable);

        if ($libelle) {
            $query->libelle($libelle);
        }

        $articles = $query->get();

        return $articles;
    }

        /**
     * @OA\Post(
     *     path="/api/v1/articles",
     *     summary="Create a new article",
     *     tags={"Articles"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/Article")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Article created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Article")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid input"
     *     )
     * )
     */
    
     public function store(StoreArticleRequest $request)
     {
         // Les données sont déjà validées par StoreArticleRequest
         $validated = $request->validated();
     
         // Utiliser le service pour créer l'article
         $article = $this->service->create($validated);
     
         return $article;
     }
    
    
    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        // Utiliser le service pour récupérer l'article
        $article = $this->service->find($id);
    
        if (!$article) {
            return response()->json(['message' => 'Article not found'], 404);
        }
    
        return $article; 
    }
    
    
    /**
     * @OA\Get(
     *     path="/api/v1/articles/{id}",
     *     summary="Get an article by ID",
     *     tags={"Articles"},
     *     @OA\Parameter(name="id", in="path", description="ID of the article", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(
     *         response=200,
     *         description="Article details",
     *         @OA\JsonContent(ref="#/components/schemas/Article")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Article not found"
     *     )
     * )
     */
    /**
     * Show the form for editing the specified resource.
     */
   
     public function update(UpdateArticleRequest $request, $id)
     {
         // Utiliser le service pour mettre à jour l'article
         $article = $this->service->update($id, $request->input('qte'));

         if (!$article) {
             return response()->json(['error' => 'Article non trouvé'], 404);
         }
         return response()->json(['article' => $article, 'message' => 'Article mis à jour avec succès']);
     }

  /**
     * @OA\Patch(
     *     path="/api/v1/articles/{id}",
     *     summary="Update an article",
     *     tags={"Articles"},
     *     @OA\Parameter(name="id", in="path", description="ID of the article to update", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *        @OA\JsonContent(ref="#/components/schemas/Article")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Article updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Article")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Article not found"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error during update"
     *     )
     * )
     */
    public function updateQuantities(UpdateArticlestockRequest $request)
    {
        // dd($request);
        $validated = $request->validated();

        // Appeler le service pour mettre à jour les quantités
        $response = $this->service->updateArticleQuantities($validated['articles']);

        return $response;
    }

    /**
     * @OA\Patch(
     *     path="/api/v1/articles/stock",
     *     summary="Update quantities of multiple articles",
     *     tags={"Articles"},
     *     @OA\RequestBody(
     *         required=true,
     *   @OA\JsonContent(ref="#/components/schemas/Article")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Articles updated successfully",
     *         @OA\JsonContent(type="object", @OA\Property(property="message", type="string"), @OA\Property(property="successful_updates", type="array", @OA\Items(type="object", @OA\Property(property="id", type="integer"), @OA\Property(property="qte", type="integer"))), @OA\Property(property="failed_updates", type="array", @OA\Items(type="object", @OA\Property(property="id", type="integer"), @OA\Property(property="qte", type="integer"))))
     *     )
     * )
     */


 
    
    public function destroy(Article $article)
    {
        $this->service->delete($article->id);
    
        return response()->json(['message' => 'Article supprimé avec succès']);
    }
/**
     * @OA\Delete(
     *     path="/api/articles/{id}",
     *     summary="Delete an article",
     *     tags={"Articles"},
     *     @OA\Parameter(name="id", in="path", description="ID of the article to delete", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(
     *         response=200,
     *         description="Article deleted successfully",
     *         @OA\JsonContent(type="object", @OA\Property(property="message", type="string"))
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Article not found"
     *     )
     * )
     */
    /**
     * Remove the specified resource from storage.
     */
 
    

    public function filter(Request $request)
    {
        $filters = $request->only(['libelle', 'qte', 'prix_unitaire']);
        $articles = $this->service->findByLibelle($filters);
        return response()->json($articles);
    }
}
