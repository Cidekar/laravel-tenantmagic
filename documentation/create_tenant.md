#### Creating a tenant

Tenantmagic, provides one approach to creating new tenants in your application. The package ```tenants``` command is underpinned by [Artisan](https://laravel.com/docs/8.x/artisan). This allows the ```tenants:create``` command to generate a new tenant in your application. Executing this command will create a new application aware tenant:

```php artisan tenants:create <new tenant name>```

The command accepts several options allowing a developer to modify the domain and tenant database name. This may be helpful if a database already exists in the system. To view a list of options you may pass the ```--help``` flag:

```
    Description:
    Create a new tenant

    Usage:
    tenants:create [options] [--] <tenant>

    Arguments:
    tenant                 The name of the tenant to create a database for

    Options:
        --domain[=DOMAIN]  A custom domain for the tenant
    -d, --dryrun           Dry run database creation
    -m, --magic            Automatically name non-unique tenant
        --id[=ID]          The id of a tenant
    -h, --help             Display this help message
    -q, --quiet            Do not output any message
    -V, --version          Display this application version
        --ansi             Force ANSI output
        --no-ansi          Disable ANSI output
    -n, --no-interaction   Do not ask any interactive question
        --env[=ENV]        The environment the command should run under
    -v|vv|vvv, --verbose   Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug
```
