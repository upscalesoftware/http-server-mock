<?php

namespace Upscale\HttpServerMock;

use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\ServerRequest;

class RequestFactory
{
    /**
     * Special value of a request field that tells to inherit value from the respective field of the request prototype
     */
    const VALUE_INHERITED = null;

    /**
     * @var ServerRequestInterface
     */
    private $requestPrototype;

    /**
     * @var StreamFactory
     */
    private $streamFactory;

    /**
     * @param ServerRequestInterface $requestPrototype
     * @param StreamFactory $streamFactory
     */
    public function __construct(ServerRequestInterface $requestPrototype, StreamFactory $streamFactory)
    {
        $this->requestPrototype = $requestPrototype;
        $this->streamFactory = $streamFactory;
    }

    /**
     * @param string|null $method
     * @param string|null $path
     * @param array $queryParams
     * @param string|null $body
     * @param array $headers
     * @return ServerRequestInterface
     */
    public function create(
        $method = self::VALUE_INHERITED,
        $path = self::VALUE_INHERITED,
        array $queryParams = [],
        $body = self::VALUE_INHERITED,
        array $headers = []
    ) {
        $prototypeUri = $this->requestPrototype->getUri();
        $prototypeBody = $this->requestPrototype->getBody();
        $methodVerb = ($method === self::VALUE_INHERITED) ? $this->requestPrototype->getMethod() : $method;
        $uri = ($path === self::VALUE_INHERITED) ? $prototypeUri : $prototypeUri->withPath($path);
        $bodyStream = ($body === self::VALUE_INHERITED) ? $prototypeBody : $this->streamFactory->create($body);
        return new ServerRequest([], [], $uri, $methodVerb, $bodyStream, $headers, [], $queryParams);
    }
}
