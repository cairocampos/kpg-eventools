<?php

use Faker\Generator as Faker;
use App\Event;

$factory->define(Event::class, function (Faker $faker) {
    return [
        'title' => $this->faker->text(20), 
        'user_id' => rand(1,10),
        'started' => now()->addDays(rand(1,10)),
        'description'  => $this->faker->text,
        'localization' => $this->faker->streetAddress
    ];
});
