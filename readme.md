<p align="center"><img src="https://user-images.githubusercontent.com/4164072/93266709-776d2800-f778-11ea-91a3-a48651b89882.png" width="516.5" height="296"></p>

# Introduction
Laravel Passport makes password grant client authentication dead-simple for your API.  Laravel Multitenancy makes a Laravel application tenant aware in minutes - feels like cheating. Tenantmagic, provides one approach to a making your Passport password grant client tenant aware. Before going through the rest of this documentation, take time to read [Laravel Multitenancy](https://spatie.be/docs/laravel-multitenancy/v1/installation/using-multiple-databases) and [Laravel Passport](https://laravel.com/docs/7.x/passport), if your unfamiliar. Tenantmagic is built on top of Laravel Passport and Laravel Multitenancy.

## Motivation
Multitenancy typically requires a user provide their authentication credentials and subdomain to get a token. Tenantmagic removes the need for a subdomain when logging into your multi-tenant application;provide a user name and password and the package will do the rest!

## Installation
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

Add the TenantmagicDomainTenantFinder to the multitenancy config:

```php
    return [
        /*
        * This class is responsible for determining which tenant should be current
        * for the given request.
        *
        * This class should extend `Spatie\Multitenancy\TenantFinder\TenantFinder`
        *
        */
        'tenant_finder' => TenantmagicDomainTenantFinder::class,

        ...

```

Add the ```TenantmagicPassportTask``` to the ```switch_tenant_tasks``` array:

```php
    return [
        /*
        * These tasks will be performed when switching tenants.
        *
        * A valid task is any class that implements Spatie\Multitenancy\Tasks\SwitchTenantTask
        */
        'switch_tenant_tasks' => [
            SwitchTenantDatabaseTask::class,
            TenantmagicPassportTask::class
        ],

        ...

```

Configure your tenant and landlord database connections:

```php
    return [
        /*
        * The connection name to reach the tenant database.
        *
        * Set to `null` to use the default connection.
        */
        'tenant_database_connection_name' => 'tenant',

        /*
        * The connection name to reach the landlord database
        */
        'landlord_database_connection_name' => 'landlord',

        ...

```

## Requirements
- Your application is configured for [multitenancy](https://spatie.be/docs/laravel-multitenancy/v1/installation/using-multiple-databases) using a separate database for each tenant
- Passport is [installed and configured](https://laravel.com/docs/7.x/passport#installation)

## Testing
To get started, create the following MySql databases:

- `laravel_tenantmagic_landlord`
- `laravel_tenantmagic_tenant_1`
- `laravel_tenantmagic_tenant_2`

Now, you may run the package's tests:

``` bash
   $ composer test

    # Runtime:       PHP 7.4.0
    # Configuration: /var/www/packages/cidekar/laravel-tenantmagic/phpunit.xml
    # Warning:       Your XML configuration validates against a deprecated schema.
    # Suggestion:    Migrate your XML configuration using "--migrate-configuration"!
    # ...
```

## Security
Please do not publicly disclose security-related issues, email packages@cidekar.com. Security vulnerabilities will be promptly addressed.

## License
Copyright 2020 Cidekar, LLC. All rights reserved.

[Apache License 2.0](./license.md)
