<?php

namespace Upscale\HttpServerMock\Body\Formatter;

class Html extends Text
{
    /**
     * @param string $value
     * @return string
     */
    public function normalize($value)
    {
        $dom = new \DOMDocument();
        $dom->preserveWhiteSpace = false;
        if ($dom->loadHTML($value)) {
            $result = $dom->saveHTML();
            if ($result !== false) {
                return $result;
            }
        }
        return parent::normalize($value);
    }
}
