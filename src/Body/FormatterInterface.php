<?php

namespace Upscale\HttpServerMock\Body;

interface FormatterInterface
{
    /**
     * @param string $value
     * @return string
     */
    public function normalize($value);
}
