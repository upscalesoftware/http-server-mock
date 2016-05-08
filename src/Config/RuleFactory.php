<?php

namespace Upscale\HttpServerMock\Config;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class RuleFactory
{
    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param int $responseDelay
     * @return Rule
     */
    public function create(ServerRequestInterface $request, ResponseInterface $response, $responseDelay)
    {
        return new Rule($request, $response, $responseDelay);
    }
}
