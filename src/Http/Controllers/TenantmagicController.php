<?php

namespace Cidekar\Tenantmagic\Http\Controllers;

use Cidekar\Tenantmagic\Tenantmagic;
use Laravel\Passport\Http\Controllers\AccessTokenController;
use Psr\Http\Message\ServerRequestInterface;
use Illuminate\Http\Request;


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

            Tenantmagic::checkClientScopes($request);

            $response = parent::issueToken($requestInterface);

            return Tenantmagic::issueResponse($response);

        } catch (OAuthServerException $exception) {
            return $this->withErrorHandling(function () use ($exception) {
                throw $exception;
            });
        }
    }
}
