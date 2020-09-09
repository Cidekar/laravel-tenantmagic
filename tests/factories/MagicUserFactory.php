<?php

use Faker\Generator;
use Cidekar\Tenantmagic\Tests\Stubs\MagicUser;


$factory->define(MagicUser::class, fn (Generator $faker) => [
    'name' => $faker->name,
    'email' => $faker->unique()->safeEmail,
    'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
]);
