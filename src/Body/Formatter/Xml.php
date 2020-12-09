<?php
/**
 * Copyright © Upscale Software. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Upscale\HttpServerMock\Body\Formatter;

class Xml extends Text
{
    /**
     * @param string $value
     * @return string
     */
    public function normalize($value)
    {
        $dom = new \DOMDocument();
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = false;

        $libxmlErrorLevel = libxml_use_internal_errors(true);

        $isLoaded = $value && $this->loadDom($dom, $value);

        libxml_clear_errors();
        libxml_use_internal_errors($libxmlErrorLevel);

        if ($isLoaded) {
            $result = $this->saveDom($dom);
            if ($result !== false) {
                return $result;
            }
        }
        return parent::normalize($value);
    }

    /**
     * @return int
     */
    protected function getDomLoadOptions()
    {
        return LIBXML_NOERROR | LIBXML_NOWARNING | LIBXML_COMPACT | LIBXML_NOBLANKS;
    }

    /**
     * @param \DOMDocument $dom
     * @param string $source
     * @return bool
     */
    protected function loadDom(\DOMDocument $dom, $source)
    {
        return $dom->loadXML($source, $this->getDomLoadOptions());
    }

    /**
     * @param \DOMDocument $dom
     * @return string|bool
     */
    protected function saveDom(\DOMDocument $dom)
    {
        return $dom->saveXML();
    }
}
