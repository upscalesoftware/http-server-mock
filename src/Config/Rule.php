<?php

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
     * @var int
     */
    private $responseDelay;

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param int $responseDelay Delay in microseconds (1 second = 1000000 microseconds)
     */
    public function __construct(ServerRequestInterface $request, ResponseInterface $response, $responseDelay = 0)
    {
        $this->request = $request;
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
