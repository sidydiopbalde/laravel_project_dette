<?php
namespace Database\Factories;

use App\Models\Article;
use Illuminate\Database\Eloquent\Factories\Factory;

class ArticleFactory extends Factory
{
    /**
     * Définir le modèle associé à la fabrique.
     *
     * @var string
     */
    protected $model = Article::class;

    /**
     * Définir l'état par défaut du modèle.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'libelle' => $this->faker->word, // Utilisez une méthode de Faker pour générer un mot
            'qte' => $this->faker->numberBetween(1, 100), // Utilisez une méthode pour générer un nombre entier
            'prix_unitaire' => $this->faker->randomFloat(2, 1, 1000), // Utilisez une méthode pour générer un nombre à virgule flottante
        ];
    }
}

