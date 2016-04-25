<?php

namespace Upscale\HttpServerMock\Config\Source;

interface SourceInterface
{
    /**
     * @return string
     */
    public function getContents();
}
