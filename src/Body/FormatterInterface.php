<?php
/**
 * Copyright © Upscale Software. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Upscale\HttpServerMock\Body;

interface FormatterInterface
{
    /**
     * @param string $value
     * @return string
     */
    public function normalize($value);
}
