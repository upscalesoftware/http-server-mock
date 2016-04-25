<?php

namespace Upscale\HttpServerMock;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

class App
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var Request\ComparatorInterface
     */
    private $comparator;

    /**
     * @param Config $config
     * @param Request\ComparatorInterface $comparator
     */
    public function __construct(Config $config, Request\ComparatorInterface $comparator)
    {
        $this->config = $config;
        $this->comparator = $comparator;
    }

    /**
     * Handle request and return appropriate response for it
     *
     * @param ServerRequestInterface $request
     * @return null|ResponseInterface
     */
    public function handle(ServerRequestInterface $request)
    {
        $response = null;
        foreach ($this->config->getRules() as $rule) {
            if ($this->comparator->isEqual($request, $rule->getRequest())) {
                $response = $rule->getResponse();
                break;
            }
        }
        return $response;
    }
}
