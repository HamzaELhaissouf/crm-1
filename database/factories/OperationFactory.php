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
            'montant' => $this->faker->numberBetween(999.99, 100009.99),
            'created_at'=> $this->faker->dateTimeBetween('2019-01-01 00:00:00', 'now'),
        ];
    }
}