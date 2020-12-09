<?php
/**
 * Copyright © Upscale Software. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Upscale\HttpServerMock\Config;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class RuleFactory
{
    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param string $requestFormat
     * @param int $responseDelay
     * @return Rule
     */
    public function create(ServerRequestInterface $request, ResponseInterface $response, $requestFormat, $responseDelay)
    {
        return new Rule($request, $response, $requestFormat, $responseDelay);
    }
}
