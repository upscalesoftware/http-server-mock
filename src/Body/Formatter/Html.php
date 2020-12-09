<?php
/**
 * Copyright © Upscale Software. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Upscale\HttpServerMock\Body\Formatter;

class Html extends Xml
{
    /**
     * @return int
     */
    protected function getDomLoadOptions()
    {
        return parent::getDomLoadOptions() | LIBXML_HTML_NODEFDTD;
    }

    /**
     * @param \DOMDocument $dom
     * @param string $source
     * @return bool
     */
    protected function loadDom(\DOMDocument $dom, $source)
    {
        return $dom->loadHTML($source, $this->getDomLoadOptions());
    }

    /**
     * @param \DOMDocument $dom
     * @return string
     */
    protected function saveDom(\DOMDocument $dom)
    {
        return $dom->saveHTML();
    }
}
