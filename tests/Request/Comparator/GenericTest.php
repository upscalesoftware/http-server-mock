<?php

namespace Upscale\HttpServerMock\Tests\Request\Comparator;

use PHPUnit_Framework_TestCase as TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;
use Upscale\HttpServerMock\Body\FormatterInterface;
use Upscale\HttpServerMock\Request;

class GenericTest extends TestCase
{
    /**
     * @var Request\Comparator\Generic
     */
    private $subject;

    /**
     * @var FormatterInterface|MockObject
     */
    private $formatter;

    /**
     * @var ServerRequestInterface|MockObject
     */
    private $fixtureRequest;

    /**
     * @var string
     */
    private $fixtureMethod = 'FIXTURE';

    /**
     * @var array
     */
    private $fixtureQueryParams = [
        'fixture_param_scalar'  => 'fixture_value',
        'fixture_param_array'   => ['fixture_item_one', 'fixture_item_two'],
    ];

    /**
     * @var array
     */
    private $fixtureHeaders = [
        'Content-Type'  => ['application/json'],
        'Accept'        => ['application/json', 'application/xml'],
    ];

    /**
     * @var array
     */
    private $fixtureCookies = [
        'SESSION_ID'    => 'dWxm4A04wWAJ',
        'banner_shown'  => '1',
    ];

    protected function setUp()
    {
        $this->fixtureRequest = $this->getRequestMock();

        $this->formatter = $this->getMock(FormatterInterface::class, [], [], '', false);
        $this->formatter->expects($this->any())->method('normalize')->willReturnCallback('strtolower');

        $this->subject = new Request\Comparator\Generic($this->formatter);
    }

    /**
     * Create a request mock instance with a given fixture values of its properties
     *
     * @param array $properties
     * @return ServerRequestInterface|MockObject
     */
    protected function getRequestMock(array $properties = [])
    {
        $fixtureUri = $this->getMock(UriInterface::class, [], [], '', false);
        $fixtureUri->expects($this->any())->method('__toString')->willReturn('/fixture/test/resource');

        $fixtureBody = $this->getMock(StreamInterface::class, [], [], '', false);
        $fixtureBody->expects($this->any())->method('getContents')->willReturn('Fixture contents');

        $properties += [
            'method'    => $this->fixtureMethod,
            'params'    => $this->fixtureQueryParams,
            'headers'   => $this->fixtureHeaders,
            'cookies'   => $this->fixtureCookies,
            'uri'       => $fixtureUri,
            'body'      => $fixtureBody,
        ];

        $result = $this->getMock(ServerRequestInterface::class, [], [], '', false);
        $result->expects($this->any())->method('getMethod')->willReturn($properties['method']);
        $result->expects($this->any())->method('getQueryParams')->willReturn($properties['params']);
        $result->expects($this->any())->method('getHeaders')->willReturn($properties['headers']);
        $result->expects($this->any())->method('getCookieParams')->willReturn($properties['cookies']);
        $result->expects($this->any())->method('getUri')->willReturn($properties['uri']);
        $result->expects($this->any())->method('getBody')->willReturn($properties['body']);

        return $result;
    }

    public function testGetFormatter()
    {
        $actualResult = $this->subject->getFormatter();

        $this->assertSame($this->formatter, $actualResult);
    }

    /**
     * @dataProvider isEqualDataProvider
     */
    public function testIsEqual(ServerRequestInterface $request, $expectedResult)
    {
        $actualResult = $this->subject->isEqual($request, $this->fixtureRequest);

        $this->assertSame($expectedResult, $actualResult);
    }

    public function isEqualDataProvider()
    {
        $request = $this->getRequestMock();

        $dataSets = [
            'full match' => [$request, true],
        ];

        return $dataSets
            + $this->isEqualMethodDataProvider()
            + $this->isEqualUriDataProvider()
            + $this->isEqualBodyDataProvider()
            + $this->isEqualQueryParamsDataProvider()
            + $this->isEqualHeadersDataProvider()
            + $this->isEqualCookiesDataProvider();
    }

    public function isEqualMethodDataProvider()
    {
        $request = $this->getRequestMock(['method' => 'CUSTOM']);

        return [
            'method mismatch' => [$request, false],
        ];
    }

    public function isEqualUriDataProvider()
    {
        $uri = $this->getMock(UriInterface::class, [], [], '', false);
        $uri->expects($this->once())->method('__toString')->willReturn('/custom/test/resource');
        $request = $this->getRequestMock(['uri' => $uri]);

        return [
            'URI mismatch' => [$request, false],
        ];
    }

    public function isEqualBodyDataProvider()
    {
        $bodyInexactMatch = $this->getMock(StreamInterface::class, [], [], '', false);
        $bodyInexactMatch->expects($this->once())->method('getContents')->willReturn('FIXTURE CONTENTS');

        $bodyMismatch = $this->getMock(StreamInterface::class, [], [], '', false);
        $bodyMismatch->expects($this->once())->method('getContents')->willReturn('Custom contents');

        $requestInexactMatch = $this->getRequestMock(['body' => $bodyInexactMatch]);
        $requestMismatch = $this->getRequestMock(['body' => $bodyMismatch]);

        return [
            'body inexact match'    => [$requestInexactMatch, true],
            'body mismatch'         => [$requestMismatch, false],
        ];
    }

    public function isEqualQueryParamsDataProvider()
    {
        $queryParamsSubset = [
            'fixture_param_scalar'  => 'fixture_value',
        ];
        $queryParamsSuperset = [
            'fixture_param_scalar'  => 'fixture_value',
            'fixture_param_array'   => ['fixture_item_one', 'fixture_item_two'],
            'extra_param'           => 'extra_value',
        ];
        $queryParamsMismatch = [
            'fixture_param_scalar'  => 'mismatching_value',
            'fixture_param_array'   => ['fixture_item_one', 'fixture_item_two'],
        ];

        $requestSubset = $this->getRequestMock(['params' => $queryParamsSubset]);
        $requestSuperset = $this->getRequestMock(['params' => $queryParamsSuperset]);
        $requestMismatch = $this->getRequestMock(['params' => $queryParamsMismatch]);

        return [
            'query params subset'   => [$requestSubset, false],
            'query params superset' => [$requestSuperset, true],
            'query params mismatch' => [$requestMismatch, false],
        ];
    }

    public function isEqualHeadersDataProvider()
    {
        $headersSubset = [
            'Accept'        => ['application/json', 'application/xml'],
        ];
        $headersSuperset = [
            'Content-Type'  => ['application/json'],
            'Accept'        => ['application/json', 'application/xml'],
            'User-Agent'    => ['Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_4) AppleWebKit/537.36 (KHTML, Gecko)'],
        ];
        $headersMismatch = [
            'Content-Type'  => ['text/html'],
            'Accept'        => ['application/json', 'application/xml'],
        ];

        $requestSubset = $this->getRequestMock(['headers' => $headersSubset]);
        $requestSuperset = $this->getRequestMock(['headers' => $headersSuperset]);
        $requestMismatch = $this->getRequestMock(['headers' => $headersMismatch]);

        return [
            'headers subset'    => [$requestSubset, false],
            'headers superset'  => [$requestSuperset, true],
            'headers mismatch'  => [$requestMismatch, false],
        ];
    }

    public function isEqualCookiesDataProvider()
    {
        $cookiesSubset = [
            'banner_shown'  => '1',
        ];
        $cookiesSuperset = [
            'SESSION_ID'    => 'dWxm4A04wWAJ',
            'banner_shown'  => '1',
            'GeoIP'         => 'US:CA:Los_Angeles',
        ];
        $cookiesMismatch = [
            'SESSION_ID'    => 'dWxm4A04wWAJ',
            'banner_shown'  => '0',
        ];

        $requestSubset = $this->getRequestMock(['cookies' => $cookiesSubset]);
        $requestSuperset = $this->getRequestMock(['cookies' => $cookiesSuperset]);
        $requestMismatch = $this->getRequestMock(['cookies' => $cookiesMismatch]);

        return [
            'cookies subset'    => [$requestSubset, false],
            'cookies superset'  => [$requestSuperset, true],
            'cookies mismatch'  => [$requestMismatch, false],
        ];
    }
}
