# Introduction
Setup a multitenancy password grant client using [Laravel Passport](https://laravel.com/docs/7.x/passport) and [Laravel Multitenancy](https://github.com/spatie/laravel-multitenancy). The goal of this package is to enable multitenancy without a lot of legwork. 

# Installation
This package can be installed with Composer:

```$ composer require ""```

Publish the configuration:

```php artisan vendor:publish --provider="" --tag="config"```
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
