<?php

namespace Upscale\HttpServerMock\Tests\Config;

use PHPUnit_Framework_TestCase as TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Upscale\HttpServerMock\Config;

class RuleFactoryTest extends TestCase
{
    /**
     * @var Config\RuleFactory
     */
    private $subject;

    protected function setUp()
    {
        $this->subject = new Config\RuleFactory();
    }

    public function testCreate()
    {
        /** @var ServerRequestInterface|MockObject $request */
        $request = $this->getMock(ServerRequestInterface::class, [], [], '', false);
        /** @var ResponseInterface|MockObject $response */
        $response = $this->getMock(ResponseInterface::class, [], [], '', false);

        $actualResult = $this->subject->create($request, $response, 3500000);

        $this->assertInstanceOf(Config\Rule::class, $actualResult);
        $this->assertSame($request, $actualResult->getRequest());
        $this->assertSame($response, $actualResult->getResponse());
        $this->assertSame(3500000, $actualResult->getResponseDelay());
    }
}
