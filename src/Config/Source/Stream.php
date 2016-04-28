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
     * @param StreamInterface $stream
     */
    public function __construct(StreamInterface $stream)
    {
        $this->stream = $stream;
    }

    /**
     * @return string
     */
    public function getContents()
    {
        return $this->stream->getContents();
    }
}
