<?php

namespace Upscale\HttpServerMock\Config\Source;

use Psr\Http\Message\StreamInterface;

class Stream implements SourceInterface
{
    /**
     * @var StreamInterface
     */
    private $stream;

    /**
     * @param array $stream
     */
    public function __construct($stream)
    {
        $this->stream = new \Zend\Diactoros\Stream($stream, 'r');
    }

    /**
     * @return string
     */
    public function getContents()
    {
        return $this->stream->getContents();
    }
}
