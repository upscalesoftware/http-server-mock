<?php

namespace Upscale\HttpServerMock\Tests;

use PHPUnit_Framework_TestCase as TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Upscale\HttpServerMock\ResponseFactory;
use Upscale\HttpServerMock\StreamFactory;

class ResponseFactoryTest extends TestCase
{
    /**
     * @var ResponseFactory
     */
    private $subject;

    /**
     * @var StreamFactory|MockObject
     */
    private $streamFactory;

    /**
     * @var StreamInterface|MockObject
     */
    private $body;

    protected function setUp()
    {
        $this->body = $this->getMock(StreamInterface::class, [], [], '', false);

        $this->streamFactory = $this->getMock(StreamFactory::class, [], [], '', false);
        $this->streamFactory->expects($this->once())->method('create')->willReturn($this->body);

        $this->subject = new ResponseFactory($this->streamFactory);
    }

    public function testCreateWithBody()
    {
        $contents = '<html><head><title>Test</title></head><body>Test contents</body></html>';
        $this->streamFactory->expects($this->once())->method('create')->with($contents)->willReturn($this->body);

        $actualResult = $this->subject->create($contents);

        $this->assertInstanceOf(ResponseInterface::class, $actualResult);
        $this->assertSame($this->body, $actualResult->getBody());
    }

    /**
     * @dataProvider createWithHeadersDataProvider
     */
    public function testCreateWithHeaders(array $headers, array $expectedHeaders)
    {
        $actualResult = $this->subject->create('', $headers);

        $this->assertInstanceOf(ResponseInterface::class, $actualResult);
        $this->assertSame($expectedHeaders, $actualResult->getHeaders());
    }

    public function createWithHeadersDataProvider()
    {
        return [
            'single-value header' => [
                ['Content-Type' => 'text/html'],
                ['Content-Type' => ['text/html']]
            ],
            'multi-value header' => [
                ['Accept' => ['application/json', 'application/xml']],
                ['Accept' => ['application/json', 'application/xml']]
            ],
        ];
    }

    /**
     * @dataProvider createWithStatusDataProvider
     */
    public function testCreateWithStatus($status, $reason, $expectedReason)
    {
        $actualResult = $this->subject->create('', [], $status, $reason);

        $this->assertInstanceOf(ResponseInterface::class, $actualResult);
        $this->assertSame($status, $actualResult->getStatusCode());
        $this->assertSame($expectedReason, $actualResult->getReasonPhrase());
    }

    public function createWithStatusDataProvider()
    {
        return [
            'custom status, auto reason'    => [404, ResponseFactory::REASON_AUTO, 'Not Found'],
            'custom status, custom reason'  => [404, 'Resource has not been found', 'Resource has not been found'],
        ];
    }
}
