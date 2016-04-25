<?php

namespace Upscale\HttpServerMock\Config\Syntax;

class Json implements ParserInterface
{
    /**
     * @param string $data
     * @return array
     */
    public function parse($data)
    {
        $result = json_decode($data, true);
        if ($result === null) {
            throw new \UnexpectedValueException('Configuration is not recognized as JSON.');
        }
        if (!is_array($result)) {
            throw new \UnexpectedValueException('Configuration is expected to declare a JSON array.');
        }
        return $result;
    }
}
