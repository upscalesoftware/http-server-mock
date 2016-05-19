<?php

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
        if ($dom->loadXML($value)) {
            $result = $dom->saveXML();
            if ($result !== false) {
                return $result;
            }
        }
        return parent::normalize($value);
    }
}
