<?php
/**
 * Copyright © Upscale Software. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Upscale\HttpServerMock;

use Psr\Http\Message\ResponseInterface;
use Zend\Diactoros\Response;

class ResponseFactory
{
    /**
     * Special value of the reason phrase field that tells to automatically determine the value from the status code
     */
    const REASON_AUTO = '';

    /**
     * @var StreamFactory
     */
    private $streamFactory;

    /**
     * @param StreamFactory $streamFactory
     */
    public function __construct(StreamFactory $streamFactory)
    {
        $this->streamFactory = $streamFactory;
    }

    /**
     * @param string $body
     * @param array $headers
     * @param int $status
     * @param string $reason
     * @return ResponseInterface
     */
    public function create($body = '', array $headers = [], $status = 200, $reason = self::REASON_AUTO)
    {
        $bodyStream = $this->streamFactory->create($body);
        $response = new Response($bodyStream, $status, $headers);
        return $response->withStatus($status, $reason);
    }
}
