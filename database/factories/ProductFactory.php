<?php

use Faker\Generator as Faker;
use App\Product;


$factory->define(Product::class, function (Faker $faker) {
    return [
        'description' => $faker->name(),
        'price' => $faker->randomFloat(2, 0 , 8)
    ];
});
