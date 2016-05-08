<?php

namespace Upscale\HttpServerMock\Tests;

use PHPUnit_Framework_TestCase as TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;
use Upscale\HttpServerMock\RequestFactory;
use Upscale\HttpServerMock\StreamFactory;

class RequestFactoryTest extends TestCase
{
    /**
     * @var RequestFactory
     */
    private $subject;

    /**
     * @var ServerRequestInterface|MockObject
     */
    private $requestPrototype;

    /**
     * @var UriInterface|MockObject
     */
    private $uriPrototype;

    /**
     * @var StreamInterface|MockObject
     */
    private $bodyPrototype;

    /**
     * @var StreamFactory|MockObject
     */
    private $streamFactory;

    protected function setUp()
    {
        $this->uriPrototype = $this->getMock(UriInterface::class, [], [], '', false);

        $this->bodyPrototype = $this->getMock(StreamInterface::class, [], [], '', false);

        $this->requestPrototype = $this->getMock(ServerRequestInterface::class, [], [], '', false);
        $this->requestPrototype->expects($this->any())->method('getUri')->willReturn($this->uriPrototype);
        $this->requestPrototype->expects($this->any())->method('getBody')->willReturn($this->bodyPrototype);

        $this->streamFactory = $this->getMock(StreamFactory::class, [], [], '', false);

        $this->subject = new RequestFactory($this->requestPrototype, $this->streamFactory);
    }

    /**
     * @dataProvider createWithMethodDataProvider
     */
    public function testCreateWithMethod($method, $expectedMethod)
    {
        $this->requestPrototype->expects($this->any())->method('getMethod')->willReturn('TEST');

        $actualResult = $this->subject->create($method);

        $this->assertInstanceOf(ServerRequestInterface::class, $actualResult);
        $this->assertSame($expectedMethod, $actualResult->getMethod());
    }

    public function createWithMethodDataProvider()
    {
        return [
            'inherited' => [RequestFactory::VALUE_INHERITED, 'TEST'],
            'custom'    => ['CUSTOM', 'CUSTOM'],
        ];
    }

    public function testCreateWithInheritedPath()
    {
        $actualResult = $this->subject->create('GET', RequestFactory::VALUE_INHERITED);

        $this->assertInstanceOf(ServerRequestInterface::class, $actualResult);
        $this->assertSame($this->uriPrototype, $actualResult->getUri());
    }

    public function testCreateWithCustomPath()
    {
        $path = '/custom/test/resource';

        $uri = $this->getMock(UriInterface::class, [], [], '', false);
        $this->uriPrototype->expects($this->once())->method('withPath')->with($path)->willReturn($uri);

        $actualResult = $this->subject->create('GET', $path);

        $this->assertInstanceOf(ServerRequestInterface::class, $actualResult);
        $this->assertSame($uri, $actualResult->getUri());
    }

    public function testCreateWithCustomParams()
    {
        $queryParams = [
            'scalar_param'  => 'fixture_value',
            'array_param'   => ['fixture_value_1', 'fixture_value_2'],
        ];

        $actualResult = $this->subject->create('GET', '/', $queryParams);

        $this->assertInstanceOf(ServerRequestInterface::class, $actualResult);
        $this->assertSame($queryParams, $actualResult->getQueryParams());
    }

    public function testCreateWithInheritedBody()
    {
        $this->streamFactory->expects($this->never())->method('create');

        $actualResult = $this->subject->create('GET', '/', [], RequestFactory::VALUE_INHERITED);

        $this->assertInstanceOf(ServerRequestInterface::class, $actualResult);
        $this->assertSame($this->bodyPrototype, $actualResult->getBody());
    }

    public function testCreateWithCustomBody()
    {
        $contents = '<html><head><title>Test</title></head><body>Test contents</body></html>';

        $stream = $this->getMock(StreamInterface::class, [], [], '', false);
        $this->streamFactory->expects($this->once())->method('create')->with($contents)->willReturn($stream);

        $actualResult = $this->subject->create('GET', '/', [], $contents);

        $this->assertInstanceOf(ServerRequestInterface::class, $actualResult);
        $this->assertSame($stream, $actualResult->getBody());
    }

    public function testCreateWithCustomHeaders()
    {
        $headers = [
            'Content-Type'  => 'application/json',
            'Accept'        => ['application/json', 'application/xml'],
        ];

        $expectedHeaders = [
            'Content-Type'  => ['application/json'],
            'Accept'        => ['application/json', 'application/xml'],
        ];

        $actualResult = $this->subject->create('GET', '/', [], RequestFactory::VALUE_INHERITED, $headers);

        $this->assertInstanceOf(ServerRequestInterface::class, $actualResult);
        $this->assertSame($expectedHeaders, $actualResult->getHeaders());
    }

    public function testCreateWithCustomCookies()
    {
        $cookies = [
            'SESSION_ID'    => 'dWxm4A04wWAJ',
            'banner_shown'  => '1',
        ];

        $actualResult = $this->subject->create('GET', '/', [], RequestFactory::VALUE_INHERITED, [], $cookies);

        $this->assertInstanceOf(ServerRequestInterface::class, $actualResult);
        $this->assertSame($cookies, $actualResult->getCookieParams());
    }
}
