<?php

namespace Upscale\HttpServerMock;

use Zend\Diactoros\Response;
use Zend\Diactoros\Server;
use Zend\Diactoros\ServerRequestFactory;

try {
    require __DIR__ . '/autoload.php';

    $request = ServerRequestFactory::fromGlobals($_SERVER + $_ENV);

    $configFile = ServerRequestFactory::get('HTTP_SERVER_MOCK_CONFIG_FILE', $request->getServerParams());
    $configType = ServerRequestFactory::get('HTTP_SERVER_MOCK_CONFIG_TYPE', $request->getServerParams(), 'json');

    $configFileChoices = $configFile ? [$configFile] : [__DIR__ . '/config.json', __DIR__ . '/config.json.dist'];

    $configFallback = new Config\Fallback();
    $configStream = $configFallback->getFirstReadableStream($configFileChoices);
    $configSource = new Config\Source\Stream($configStream);

    $placeholders = [
        '%base_dir%' => __DIR__,
        '%document_root%' => ServerRequestFactory::get('DOCUMENT_ROOT', $request->getServerParams()),
        '%base_url_path%' => dirname(ServerRequestFactory::get('SCRIPT_NAME', $request->getServerParams())),
    ];
    $configSource = new Config\Source\Decorator\SubstringSubstitution($configSource, $placeholders);

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

    $formatDetector = new Request\FormatDetector(require __DIR__ . '/mime-types.php', 'binary');

    $app = new App($config, $formatDetector, $comparatorManager);

    $responseNotFound = new Response('php://memory', 400, ['Content-Type' => 'text/plain']);

    $server = new Server([$app, 'handle'], $request, $responseNotFound);
    $server->listen();

} catch (\Exception $e) {
    header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
    header('Content-Type: text/plain');
    echo 'Error: ' . $e->getMessage();
}
