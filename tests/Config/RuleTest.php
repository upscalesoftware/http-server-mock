<?php

namespace Upscale\HttpServerMock\Tests\Config;

use PHPUnit_Framework_TestCase as TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Upscale\HttpServerMock\Config;

class RuleTest extends TestCase
{
    /**
     * @var Config\Rule
     */
    private $subject;

    /**
     * @var ServerRequestInterface|MockObject
     */
    private $request;

    /**
     * @var ResponseInterface|MockObject
     */
    private $response;

    protected function setUp()
    {
        $this->request = $this->getMock(ServerRequestInterface::class, [], [], '', false);
        $this->response = $this->getMock(ResponseInterface::class, [], [], '', false);

        $this->subject = new Config\Rule($this->request, $this->response, 'fixture', 3500000);
    }

    public function testGetRequest()
    {
        $this->assertSame($this->request, $this->subject->getRequest());
    }

    public function testGetRequestFormat()
    {
        $this->assertSame('fixture', $this->subject->getRequestFormat());
    }

    public function testGetResponse()
    {
        $this->assertSame($this->response, $this->subject->getResponse());
    }

    public function testGetResponseDelay()
    {
        $this->assertSame(3500000, $this->subject->getResponseDelay());
    }
}
