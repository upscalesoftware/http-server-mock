<?php
/**
 * Copyright © Upscale Software. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Upscale\HttpServerMock\Config;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Rule
{
    /**
     * @var ServerRequestInterface
     */
    private $request;

    /**
     * @var ResponseInterface
     */
    private $response;

    /**
     * @var string
     */
    private $requestFormat;

    /**
     * @var int
     */
    private $responseDelay;

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param string $requestFormat
     * @param int $responseDelay Delay in microseconds (1 second = 1000000 microseconds)
     */
    public function __construct(
        ServerRequestInterface $request,
        ResponseInterface $response,
        $requestFormat,
        $responseDelay = 0
    ) {
        $this->request = $request;
        $this->requestFormat = $requestFormat;
        $this->response = $response;
        $this->responseDelay = $responseDelay;
    }

    /**
     * @return ServerRequestInterface
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @return string
     */
    public function getRequestFormat()
    {
        return $this->requestFormat;
    }

    /**
     * @return ResponseInterface
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Retrieve delay duration in microseconds
     *
     * @return int
     */
    public function getResponseDelay()
    {
        return $this->responseDelay;
    }
}
