<?php

use Faker\Generator as Faker;
use App\Custumer;

$factory->define(Custumer::class, function (Faker $faker) {
    return [
        'name'=> $faker->name()
    ];
});
