<?php

namespace Database\Factories;

use App\Operation;
use Illuminate\Database\Eloquent\Factories\Factory;

class OperationFactory extends Factory 
{
    protected $model = Operation::class;

    public function definition()
    {
        return [
            'type' => $this->faker->randomElement(['sell', 'buy']),
            'prix_achat' => $this->faker->numberBetween(10.99, 100.99),
            'quantity' => $this->faker->numberBetween(5,100),
            'created_at'=> $this->faker->dateTimeBetween('2019-01-01 00:00:00', 'now'),
        ];
    }
}