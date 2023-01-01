<?php

namespace Database\Factories;

use App\Models\Loan;
use Faker\Generator as Faker;

$factory->define(Loan::class, function (Faker $faker) {
    return [
        'amount' => $faker->amount,
        'term' => $faker->term
    ];
});
