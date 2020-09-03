<?php

    return [

        /*
        |--------------------------------------------------------------------------
        | Route Prefix
        |--------------------------------------------------------------------------
        |
        | Tenantmagic provides routes for creating tokens for your application. By
        | default, this route configuration and can be changed by providing a new
        | values here.
        |
        */
        'route' => [
            'prefix' => 'api/v1/oauth/',
            'name' => 'token'
        ],

        /*
        |--------------------------------------------------------------------------
        | Scopes
        |--------------------------------------------------------------------------
        |
        | Tenantmagic provides a wrapper around Passport token scopes to make a
        | grant client request a specific set of permission when authorizing. By
        | default, this option is not required however might be helpful to limit
        | the actions of grant client.
        |
        */
        'allowWildCardScopes' => true,
    ];
