<?php

namespace Upscale\HttpServerMock;

use Zend\Diactoros\Response;
use Zend\Diactoros\ServerRequestFactory;

try {
    require __DIR__ . '/autoload.php';

    $request = ServerRequestFactory::fromGlobals($_SERVER + $_ENV);

    $configFile = ServerRequestFactory::get('HTTP_SERVER_MOCK_CONFIG_FILE', $request->getServerParams());
    $configType = ServerRequestFactory::get('HTTP_SERVER_MOCK_CONFIG_TYPE', $request->getServerParams(), 'json');

    $configFileChoices = $configFile ? [$configFile] : [__DIR__ . '/config.json', __DIR__ . '/config.json.dist'];

    $configPlaceholders = [
        '%base_dir%' => __DIR__,
        '%document_root%' => ServerRequestFactory::get('DOCUMENT_ROOT', $request->getServerParams()),
        '%base_url_path%' => dirname(ServerRequestFactory::get('SCRIPT_NAME', $request->getServerParams())),
    ];

    $formatDetector = new Request\FormatDetector(require __DIR__ . '/mime-types.php', 'binary');

    $defaultResponse = new Response('php://memory', 400, ['Content-Type' => 'text/plain']);

    $serverFactory = new ServerFactory();
    $server = $serverFactory->create(
        $request, $formatDetector, $defaultResponse, $configFileChoices, $configType, $configPlaceholders
    );
    $server->listen();

} catch (\Exception $e) {
    header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
    header('Content-Type: text/plain');
    echo 'Error: ' . $e->getMessage();
}
