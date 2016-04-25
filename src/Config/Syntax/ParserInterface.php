<?php

namespace Upscale\HttpServerMock\Config\Syntax;

interface ParserInterface
{
    /**
     * @param string $data
     * @return array
     */
    public function parse($data);
}
