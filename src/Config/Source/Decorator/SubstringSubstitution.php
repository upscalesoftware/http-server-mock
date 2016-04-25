<?php

namespace Upscale\HttpServerMock\Config\Source\Decorator;

use Upscale\HttpServerMock\Config\Source\SourceInterface;

class SubstringSubstitution implements SourceInterface
{
    /**
     * @var SourceInterface
     */
    private $subject;

    /**
     * @var array|string[]
     */
    private $search = [];

    /**
     * @var array|string[]
     */
    private $replace = [];

    /**
     * @param SourceInterface $subject
     * @param array $map
     */
    public function __construct(SourceInterface $subject, array $map)
    {
        $this->subject = $subject;
        $this->search = array_keys($map);
        $this->replace = array_values($map);
    }

    /**
     * @return string
     */
    public function getContents()
    {
        return str_replace($this->search, $this->replace, $this->subject->getContents());
    }
}
