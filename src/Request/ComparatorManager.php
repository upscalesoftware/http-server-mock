<?php

namespace Upscale\HttpServerMock\Request;

use Upscale\HttpServerMock\Body\FormatterRegistry;

class ComparatorManager
{
    /**
     * @var FormatterRegistry
     */
    private $formatterRegistry;

    /**
     * @var Comparator\GenericMutable
     */
    private $comparatorFlyweight;

    /**
     * @param FormatterRegistry $formatterRegistry
     */
    public function __construct(FormatterRegistry $formatterRegistry)
    {
        $this->formatterRegistry = $formatterRegistry;
    }

    /**
     * @param string $format
     * @return ComparatorInterface
     */
    public function getComparator($format)
    {
        $formatter = $this->formatterRegistry->get($format);
        if (!$this->comparatorFlyweight) {
            $this->comparatorFlyweight = new Comparator\GenericMutable($formatter);
        } else {
            $this->comparatorFlyweight->setFormatter($formatter);
        }
        return $this->comparatorFlyweight;
    }
}
