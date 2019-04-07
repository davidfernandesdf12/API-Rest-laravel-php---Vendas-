<?php

use Faker\Generator as Faker;
use App\Seller;

$factory->define(Seller::class, function (Faker $faker) {
    return [
        'name'=> $faker->name()
    ];
});
