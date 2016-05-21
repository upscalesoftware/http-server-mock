<?php

namespace Upscale\HttpServerMock\Request\Comparator;

use Upscale\HttpServerMock\Body\FormatterInterface;
use Upscale\HttpServerMock\Request\ComparatorInterface;
use Psr\Http\Message\ServerRequestInterface;

class Generic implements ComparatorInterface
{
    /**
     * @var FormatterInterface
     */
    protected $formatter;

    /**
     * @param FormatterInterface $formatter
     */
    public function __construct(FormatterInterface $formatter)
    {
        $this->formatter = $formatter;
    }

    /**
     * @return FormatterInterface
     */
    public function getFormatter()
    {
        return $this->formatter;
    }

    /**
     * @param ServerRequestInterface $subject
     * @param ServerRequestInterface $exemplar
     * @return bool
     */
    public function isEqual(ServerRequestInterface $subject, ServerRequestInterface $exemplar)
    {
        if ($subject->getMethod() != $exemplar->getMethod()) {
            return false;
        }
        if ($subject->getUri()->__toString() != $exemplar->getUri()->__toString()) {
            return false;
        }
        if (!$this->isContentsEqual($subject->getBody()->getContents(), $exemplar->getBody()->getContents())) {
            return false;
        }
        if (!$this->hasAll($subject->getQueryParams(), $exemplar->getQueryParams())) {
            return false;
        }
        if (!$this->hasAll($subject->getHeaders(), $exemplar->getHeaders())) {
            return false;
        }
        if (!$this->hasAll($subject->getCookieParams(), $exemplar->getCookieParams())) {
            return false;
        }
        return true;
    }

    /**
     * @param string $subject
     * @param string $exemplar
     * @return bool
     */
    protected function isContentsEqual($subject, $exemplar)
    {
        return ($subject == $exemplar)
            || ($this->formatter->normalize($subject) == $this->formatter->normalize($exemplar));
    }

    /**
     * @param array $haystack
     * @param array $needle
     * @return bool
     */
    protected function hasAll(array $haystack, array $needle)
    {
        foreach ($needle as $paramName => $paramValue) {
            if (!array_key_exists($paramName, $haystack) || $haystack[$paramName] != $paramValue) {
                return false;
            }
        }
        return true;
    }
}
