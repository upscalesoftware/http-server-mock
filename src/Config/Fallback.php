<?php

namespace Upscale\HttpServerMock\Config;

use Psr\Http\Message\StreamInterface;

class Fallback
{
    /**
     * @param array $streams
     * @return StreamInterface
     */
    public function getFirstReadableStream(array $streams)
    {
        foreach ($streams as $stream) {
            try {
                return new \Zend\Diactoros\Stream($stream, 'r');
            } catch (\Exception $e) {
                continue;
            }
        }
        throw new \RuntimeException(
            sprintf('Unable to locate a readable stream amongst: %s.', implode(', ', $streams))
        );
    }
}
