# Introduction
Laravel Passport makes password grant client authentication dead-simple for your API.  Laravel Multitenancy makes a Laravel application tenant aware in minutes - feels like cheating. Tenantmagic, provides one approach to a making your Passport password grant client tenant aware. Before going through the rest of this documentation, take time to read [Laravel Multitenancy](https://spatie.be/docs/laravel-multitenancy/v1/installation/using-multiple-databases) and [Laravel Passport](https://laravel.com/docs/7.x/passport), if your unfamiliar. Tenantmagic is built on top of Laravel Passport and Laravel Multitenancy.

# Installation
This package can be installed with Composer:

```$ composer require "cidekar/tenantmagic:^1.0"```

Publish the configuration:

```php artisan vendor:publish --provider="Cidekar\Tenantmagic\TeanantmagicServiceProvider" --tag="config"```
The configuration will be published to config/tenantmagic.php

The User model must use ```UsesPassportModelMagic```:

```php
use Cidekar\Tenantmagic\Models\Concerns\UsesPassportModelMagic;

class User extends Authenticatable
{
    use UsesPassportModelMagic;
    ...
```
Please keep in mind this package assumes the grant client is stored in the landlord database. Users are stored in the tenant database.

# Requirements
- Your application is configured for [multitenancy](https://spatie.be/docs/laravel-multitenancy/v1/installation/using-multiple-databases) using a separate database for each tenant
- Passport is [installed and configured](https://laravel.com/docs/7.x/passport#installation)