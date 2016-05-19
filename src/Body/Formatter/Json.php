<?php

namespace Upscale\HttpServerMock\Body\Formatter;

class Json extends Text
{
    /**
     * @param string $value
     * @return string
     */
    public function normalize($value)
    {
        $parsed = json_decode($value);
        if ($parsed !== null) {
            return json_encode($parsed);
        }
        return parent::normalize($value);
    }
}
