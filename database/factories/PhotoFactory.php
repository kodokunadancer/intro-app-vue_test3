<?php

declare(strict_types=1);

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(App\Photo::class, function (Faker $faker) {
    return [
        'random_id' => Str::random(12),
        'profile_id' => fn() => factory(App\Profile::class)->create()->id,
        'group_id' => fn() => factory(App\Group::class)->create()->id,
        'filename' => Str::random(12) . '.jpg',
        'created_at' => $faker->dateTime(),
        'updated_at' => $faker->dateTime(),
    ];
});
