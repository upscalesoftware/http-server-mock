<?php

namespace Upscale\HttpServerMock\Body;

class FormatterRegistry
{
    /**
     * @var array|FormatterInterface[]
     */
    private $instances;

    /**
     * @param array|FormatterInterface[] $map
     */
    public function __construct(array $map = [])
    {
        // Assign with type enforcement
        foreach ($map as $format => $formatter) {
            $this->add($format, $formatter);
        }
    }

    /**
     * @param string $format
     * @param FormatterInterface $formatter
     */
    public function add($format, FormatterInterface $formatter)
    {
        if (isset($this->instances[$format])) {
            throw new \InvalidArgumentException("Format '$format' has already been registered.");
        }
        $this->instances[$format] = $formatter;
    }

    /**
     * @param string $format
     * @return FormatterInterface
     */
    public function get($format)
    {
        if (!isset($this->instances[$format])) {
            throw new \InvalidArgumentException("Format '$format' is not recognized.");
        }
        return $this->instances[$format];
    }
}
