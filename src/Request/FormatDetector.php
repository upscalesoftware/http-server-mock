<?php

namespace Upscale\HttpServerMock\Request;

use Psr\Http\Message\ServerRequestInterface;

class FormatDetector
{
    /**
     * @var array
     */
    private $mimeTypeFormatMap;

    /**
     * @var string
     */
    private $defaultFormat;

    /**
     * @param array $mimeTypeFormatMap
     * @param string $defaultFormat
     */
    public function __construct(array $mimeTypeFormatMap, $defaultFormat)
    {
        $this->mimeTypeFormatMap = $mimeTypeFormatMap;
        $this->defaultFormat = $defaultFormat;
    }

    /**
     * @param ServerRequestInterface $request
     * @return string
     */
    public function getFormat(ServerRequestInterface $request)
    {
        $contentType = $request->getHeaderLine('Content-Type');
        if ($contentType && array_key_exists($contentType, $this->mimeTypeFormatMap)) {
            return $this->mimeTypeFormatMap[$contentType];
        }
        return $this->defaultFormat;
    }
}
