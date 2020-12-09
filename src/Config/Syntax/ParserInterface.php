<?php
/**
 * Copyright © Upscale Software. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Upscale\HttpServerMock\Config\Syntax;

interface ParserInterface
{
    /**
     * @param string $data
     * @return array
     */
    public function parse($data);
}
