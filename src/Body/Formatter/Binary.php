<?php

namespace Upscale\HttpServerMock\Body\Formatter;

use Upscale\HttpServerMock\Body\FormatterInterface;

class Binary implements FormatterInterface
{
    /**
     * @param string $value
     * @return string
     */
    public function normalize($value)
    {
        return $value;
    }
}
