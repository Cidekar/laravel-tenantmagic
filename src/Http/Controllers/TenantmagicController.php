<?php

namespace Cidekar\Tenantmagic\Http\Controllers;

use Laravel\Passport\Http\Controllers\AccessTokenController;
use Psr\Http\Message\ServerRequestInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;   


class TenantmagicController extends AccessTokenController
{
    /**
     * Authorize a client to access the user's account.
     *
     * @param  \Psr\Http\Message\ServerRequestInterface  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ServerRequestInterface $requestInterface, Request $request)
    {
        try {
            if(!config('tenantmagic.allowWildCardScopes'))
            {
                if ($request->get('scope') === '*') {
                    return response('Requested scopes must be unique.', 400);
                }
            }
            $response = parent::issueToken($requestInterface);
            $tenant = Route::getBindingCallback('tenant');
            return $response->withHeaders([
                'X-Heroic-Domain' => $tenant->domain,
            ]);
        } catch (OAuthServerException $exception) {
            return $this->withErrorHandling(function () use ($exception) {
                throw $exception;
            });
        }
    }
}
