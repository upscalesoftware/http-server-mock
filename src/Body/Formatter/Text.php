<?php

namespace Upscale\HttpServerMock\Body\Formatter;

use Upscale\HttpServerMock\Body\FormatterInterface;

class Text implements FormatterInterface
{
    /**
     * @var array
     */
    protected $newlineSignatures = [
        "\r\n",
        "\n\r",
        "\r",
        "\n",
    ];

    /**
     * @param string $value
     * @return string
     */
    public function normalize($value)
    {
        return str_replace($this->newlineSignatures, "\n", trim($value));
    }
}
