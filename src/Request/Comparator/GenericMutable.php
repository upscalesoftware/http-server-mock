<?php

namespace Upscale\HttpServerMock\Request\Comparator;

use Upscale\HttpServerMock\Body\FormatterInterface;

class GenericMutable extends Generic
{
    /**
     * @param FormatterInterface $formatter
     */
    public function setFormatter(FormatterInterface $formatter)
    {
        $this->formatter = $formatter;
    }
}
