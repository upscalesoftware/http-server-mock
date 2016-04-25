<?php

namespace Upscale\HttpServerMock;

use Psr\Http\Message\StreamInterface;
use Zend\Diactoros\Stream;

class StreamFactory
{
    /**
     * @param string $data
     * @return StreamInterface
     */
    public function create($data)
    {
        /**
         * Support of the native PHP streams
         * @link http://us3.php.net/wrappers
         */
        if (preg_match('#^[[:alnum:]]+(?=://)#', $data)) {
            $stream = $data;
        } else {
            $stream = 'data://text/plain;base64,' . base64_encode($data);
        }
        return new Stream($stream);
    }
}
