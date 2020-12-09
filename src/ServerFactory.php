<?php
/**
 * Copyright © Upscale Software. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Upscale\HttpServerMock;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Server;

class ServerFactory
{
    /**
     * @param ServerRequestInterface $request
     * @param Request\FormatDetector $formatDetector
     * @param ResponseInterface $defaultResponse
     * @param array $configFileChoices
     * @param string $configType
     * @param array $configPlaceholders
     * @return Server
     */
    public function create(
        ServerRequestInterface $request,
        Request\FormatDetector $formatDetector,
        ResponseInterface $defaultResponse,
        array $configFileChoices,
        $configType,
        array $configPlaceholders = []
    ) {
        $configFallback = new Config\Fallback();
        $configStream = $configFallback->getFirstReadableStream($configFileChoices);
        $configSource = new Config\Source\Stream($configStream);
        $configSource = new Config\Source\Decorator\SubstringSubstitution($configSource, $configPlaceholders);

        $streamFactory = new StreamFactory();
        $requestFactory = new RequestFactory($request, $streamFactory);
        $responseFactory = new ResponseFactory($streamFactory);

        $configParserFactory = new Config\Syntax\ParserFactory();
        $configParser = $configParserFactory->create($configType);
        $configRuleFactory = new Config\RuleFactory();

        $config = new Config($configSource, $configParser, $requestFactory, $responseFactory, $configRuleFactory);

        $formatterRegistry = new Body\FormatterRegistry([
            'binary'    => new Body\Formatter\Binary(),
            'html'      => new Body\Formatter\Html(),
            'json'      => new Body\Formatter\Json(),
            'text'      => new Body\Formatter\Text(),
            'xml'       => new Body\Formatter\Xml(),
        ]);

        $comparatorManager = new Request\ComparatorManager($formatterRegistry);

        $app = new App($config, $formatDetector, $comparatorManager);

        $server = new Server([$app, 'handle'], $request, $defaultResponse);

        return $server;
    }
}
