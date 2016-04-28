<?php

namespace Upscale\HttpServerMock;

use Zend\Diactoros\Response;
use Zend\Diactoros\Server;
use Zend\Diactoros\ServerRequestFactory;

try {
    $autoloadScript = __DIR__ . '/vendor/autoload.php';
    if (!file_exists($autoloadScript)) {
        throw new \RuntimeException('Installation is incomplete. Please run "composer install".');
    }
    require $autoloadScript;

    $request = ServerRequestFactory::fromGlobals();

    $configFile = ServerRequestFactory::get('HTTP_SERVER_MOCK_CONFIG_FILE', $request->getServerParams());
    $configType = ServerRequestFactory::get('HTTP_SERVER_MOCK_CONFIG_TYPE', $request->getServerParams(), 'json');

    $configFileChoices = $configFile ? [$configFile] : [__DIR__ . '/config.json', __DIR__ . '/config.json.dist'];

    $configFallback = new Config\Fallback();
    $configStream = $configFallback->getFirstReadableStream($configFileChoices);
    $configSource = new Config\Source\Stream($configStream);

    $placeholders = [
        '%base_dir%' => __DIR__,
        '%base_url_path%' => dirname(ServerRequestFactory::get('SCRIPT_NAME', $request->getServerParams())),
    ];
    $configSource = new Config\Source\Decorator\SubstringSubstitution($configSource, $placeholders);

    $streamFactory = new StreamFactory();
    $requestFactory = new RequestFactory($request, $streamFactory);
    $responseFactory = new ResponseFactory($streamFactory);

    $configParserFactory = new Config\Syntax\ParserFactory();
    $configParser = $configParserFactory->create($configType);

    $config = new Config($configSource, $configParser, $requestFactory, $responseFactory);

    $app = new App($config, new Request\Comparator\Generic());

    $responseNotFound = new Response('php://memory', 400, ['Content-Type' => 'text/plain']);

    $server = new Server([$app, 'handle'], $request, $responseNotFound);
    $server->listen();

} catch (\Exception $e) {
    header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
    header('Content-Type: text/plain');
    echo 'Error: ' . $e->getMessage();
}
