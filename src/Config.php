<?php
/**
 * Copyright © Upscale Software. All rights reserved.
 * See LICENSE.txt for license details.
 */
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
     * @var Config\RuleFactory
     */
    private $ruleFactory;

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
        'format'    => null,
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
     * @param Config\RuleFactory $ruleFactory
     */
    public function __construct(
        SourceInterface $configSource,
        ParserInterface $syntaxParser,
        RequestFactory $requestFactory,
        ResponseFactory $responseFactory,
        Config\RuleFactory $ruleFactory
    ) {
        $this->configSource = $configSource;
        $this->syntaxParser = $syntaxParser;
        $this->requestFactory = $requestFactory;
        $this->responseFactory = $responseFactory;
        $this->ruleFactory = $ruleFactory;
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
                $requestInfo['headers'],
                $requestInfo['cookies']
            );

            $response = $this->responseFactory->create(
                $responseInfo['body'],
                $responseInfo['headers'],
                $responseInfo['status'],
                $responseInfo['reason']
            );

            $responseDelay = $this->convertMilliToMicro($responseInfo['delay']);

            $result[] = $this->ruleFactory->create($request, $response, $requestInfo['format'], $responseDelay);
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
}
