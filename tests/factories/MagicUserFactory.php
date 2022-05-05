<?php

use Faker\Generator;
use Cidekar\Tenantmagic\Tests\Stubs\MagicUser;
use Illuminate\Support\Facades\Hash;

$factory->define(MagicUser::class, fn (Generator $faker) => [
    'name' => $faker->name,
    'email' => $faker->unique()->safeEmail,
    'password' =>  Hash::make('123')
]);
