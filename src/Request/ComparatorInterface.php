<?php

namespace Upscale\HttpServerMock\Request;

use Psr\Http\Message\ServerRequestInterface;

interface ComparatorInterface
{
    /**
     * @param ServerRequestInterface $subject
     * @param ServerRequestInterface $exemplar
     * @return bool
     */
    public function isEqual(ServerRequestInterface $subject, ServerRequestInterface $exemplar);
}
