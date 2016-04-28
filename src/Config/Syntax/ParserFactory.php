<?php

namespace Upscale\HttpServerMock\Config\Syntax;

class ParserFactory
{
    /**
     * @var string
     */
    private $defaultNamespace;

    /**
     * @param string $defaultNamespace
     */
    public function __construct($defaultNamespace = __NAMESPACE__)
    {
        $this->defaultNamespace = $defaultNamespace;
    }

    /**
     * @param string $type
     * @return ParserInterface
     */
    public function create($type)
    {
        $class = class_exists($type) ? $type : $this->buildClassName($type);
        if (!class_exists($class)) {
            throw new \InvalidArgumentException(sprintf('Configuration syntax "%s" is not recognized.', $type));
        }
        $result = new $class();
        if (!$result instanceof ParserInterface) {
            throw new \UnexpectedValueException(sprintf('Implementation of "%s" is expected.', ParserInterface::class));
        }
        return $result;
    }

    /**
     * @param string $type
     * @return string
     */
    protected function buildClassName($type)
    {
        return $this->defaultNamespace . '\\' .  ucfirst(strtolower($type));
    }
}
