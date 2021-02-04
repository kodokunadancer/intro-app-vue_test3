<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;

$factory->define(App\Comment::class, function (Faker $faker) {
    return [
        'active_profile_id' => function() {
          return factory(App\Profile::class)->create()->id;
        },
        'passive_profile_id' => function() {
          return factory(App\Profile::class)->create()->id;
        },
        'content' => substr($faker->text, 0, 500),
        'created_at' => $faker->dateTime(),
        'updated_at' => $faker->dateTime(),
    ];
});
