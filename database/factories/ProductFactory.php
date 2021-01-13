<?php

namespace Database\Factories;

use App\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'designation' => $this->faker->name,
            'prix_de_vente' => $this->faker->numberBetween(10, 100),
            'stock_initial' => $this->faker->numberBetween(201, 1381),
            'stock_actuel' => $this->faker->numberBetween(21, 901),
            'stock_min' => $this->faker->numberBetween(21, 100),
            'prix_de_dachat' => $this->faker->numberBetween(10, 89),
            'montant' => $this->faker->numberBetween(12, 129),
            'image' => $this->faker->imageUrl(),
        ];
    }
}
