<?php

use Faker\Generator as Faker;
use Cidekar\Tenantmagic\Tests\Stubs\MagicTenant;

$factory->define(MagicTenant::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'domain' => $faker->unique()->domainName,
        'database' => $faker->userName,
    ];
});
