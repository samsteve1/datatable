<?php

use Faker\Generator as Faker;

$factory->define(App\Plan::class, function (Faker $faker) {
    return [
        'paystack_id' => str_random(10),
        'price' => random_int(1, 1000),
        'active' => false
    ];
});
