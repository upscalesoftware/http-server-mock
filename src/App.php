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
     * @var Request\FormatDetector
     */
    private $formatDetector;

    /**
     * @var Request\ComparatorManager
     */
    private $comparatorManager;

    /**
     * @param Config $config
     * @param Request\FormatDetector $formatDetector
     * @param Request\ComparatorManager $comparatorManager
     */
    public function __construct(
        Config $config,
        Request\FormatDetector $formatDetector,
        Request\ComparatorManager $comparatorManager
    ) {
        $this->config = $config;
        $this->formatDetector = $formatDetector;
        $this->comparatorManager = $comparatorManager;
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
        $detectedFormat = $this->formatDetector->getFormat($request);
        foreach ($this->config->getRules() as $rule) {
            $format = $rule->getRequestFormat() ?: $detectedFormat;
            $comparator = $this->comparatorManager->getComparator($format);
            if ($comparator->isEqual($request, $rule->getRequest())) {
                $this->idle($rule->getResponseDelay());
                $response = $rule->getResponse();
                break;
            }
        }
        return $response;
    }

    /**
     * Delay program execution for a given duration of time
     *
     * @param int $duration Duration in microseconds
     */
    protected function idle($duration = 0)
    {
        if ($duration > 0) {
            usleep($duration);
        }
    }
}
