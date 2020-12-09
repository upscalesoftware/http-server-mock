<?php
/**
 * Copyright © Upscale Software. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Upscale\HttpServerMock\Config\Source;

interface SourceInterface
{
    /**
     * @return string
     */
    public function getContents();
}
