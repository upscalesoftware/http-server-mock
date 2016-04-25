<?php

namespace Upscale\HttpServerMock;

try {
    $autoloadScript = __DIR__ . '/vendor/autoload.php';
    if (!file_exists($autoloadScript)) {
        throw new \RuntimeException('Application is not installed. Please run "composer install".');
    }
    require $autoloadScript;

    $request = \Zend\Diactoros\ServerRequestFactory::fromGlobals($_SERVER);
    $response = new \Zend\Diactoros\Response('php://memory', 400, ['Content-Type' => 'text/plain']);

    try {
        $configSource = new Config\Source\Stream(__DIR__ . '/config.json');
    } catch (\Exception $e) {
        $configSource = new Config\Source\Stream(__DIR__ . '/config.json.dist');
    }

    $placeholders = [
        '%base_dir%' => __DIR__,
        '%base_url_path%' => dirname($request->getServerParams()['SCRIPT_NAME']),
    ];
    $configSource = new Config\Source\Decorator\SubstringSubstitution($configSource, $placeholders);

    $streamFactory = new StreamFactory();
    $requestFactory = new RequestFactory($request, $streamFactory);
    $responseFactory = new ResponseFactory($streamFactory);

    $config = new Config($configSource, new Config\Syntax\Json(), $requestFactory, $responseFactory);

    $app = new App($config, new Request\Comparator\Generic());

    $server = new \Zend\Diactoros\Server([$app, 'handle'], $request, $response);
    $server->listen();
} catch (\Exception $e) {
    header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
    header('Content-Type: text/plain');
    echo 'Error: ' . $e->getMessage();
}
