<?php
/**
 * Copyright © Upscale Software. All rights reserved.
 * See LICENSE.txt for license details.
 */

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
