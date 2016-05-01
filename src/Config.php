<?php

namespace Upscale\HttpServerMock;

use Upscale\HttpServerMock\Config\Source\SourceInterface;
use Upscale\HttpServerMock\Config\Syntax\ParserInterface;

class Config
{
    /**
     * @var SourceInterface
     */
    private $configSource;

    /**
     * @var ParserInterface
     */
    private $syntaxParser;

    /**
     * @var RequestFactory
     */
    private $requestFactory;

    /**
     * @var ResponseFactory
     */
    private $responseFactory;

    /**
     * @var array|Config\Rule[]
     */
    private $rules;

    /**
     * @var array
     */
    protected $requestDefaults = [
        'method'    => RequestFactory::VALUE_INHERITED,
        'path'      => RequestFactory::VALUE_INHERITED,
        'params'    => [],
        'headers'   => [],
        'cookies'   => [],
        'body'      => RequestFactory::VALUE_INHERITED,
    ];

    /**
     * @var array
     */
    protected $responseDefaults = [
        'status'    => 200,
        'reason'    => ResponseFactory::REASON_AUTO,
        'headers'   => [],
        'body'      => '',
        'delay'     => 0,
    ];

    /**
     * @param SourceInterface $configSource
     * @param ParserInterface $syntaxParser
     * @param RequestFactory $requestFactory
     * @param ResponseFactory $responseFactory
     */
    public function __construct(
        SourceInterface $configSource,
        ParserInterface $syntaxParser,
        RequestFactory $requestFactory,
        ResponseFactory $responseFactory
    ) {
        $this->configSource = $configSource;
        $this->syntaxParser = $syntaxParser;
        $this->requestFactory = $requestFactory;
        $this->responseFactory = $responseFactory;
    }

    /**
     * @return array|Config\Rule[]
     */
    public function getRules()
    {
        if ($this->rules === null) {
            $this->rules = $this->buildRules($this->syntaxParser->parse($this->configSource->getContents()));
        }
        return $this->rules;
    }

    /**
     * @param array $parsedConfig
     * @return array|Config\Rule[]
     */
    protected function buildRules(array $parsedConfig)
    {
        $result = [];
        foreach ($parsedConfig as $ruleInfo) {
            $requestInfo = isset($ruleInfo['request']) ? (array)$ruleInfo['request'] : [];
            $requestInfo += $this->requestDefaults;

            $responseInfo = isset($ruleInfo['response']) ? (array)$ruleInfo['response'] : [];
            $responseInfo += $this->responseDefaults;

            $request = $this->requestFactory->create(
                $requestInfo['method'],
                $requestInfo['path'],
                $requestInfo['params'],
                $requestInfo['body'],
                $this->normalizeHeaders($requestInfo['headers']),
                $requestInfo['cookies']
            );

            $response = $this->responseFactory->create(
                $responseInfo['body'],
                $responseInfo['headers'],
                $responseInfo['status'],
                $responseInfo['reason']
            );

            $result[] = new Config\Rule($request, $response, $this->convertMilliToMicro($responseInfo['delay']));
        }
        return $result;
    }

    /**
     * Convert a number from milli- to micro- units
     *
     * @param int $value
     * @return int
     */
    protected function convertMilliToMicro($value)
    {
        return $value * 1000;
    }

    /**
     * @param array $headers
     * @return array
     * @see \Zend\Diactoros\MessageTrait::filterHeaders
     */
    protected function normalizeHeaders(array $headers)
    {
        $result = [];
        foreach ($headers as $key => $value) {
            $result[strtolower($key)] = $value;
        }
        return $result;
    }
}
