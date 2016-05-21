<?php

namespace Upscale\HttpServerMock\Tests\Request;

use PHPUnit_Framework_TestCase as TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Psr\Http\Message\ServerRequestInterface;
use Upscale\HttpServerMock\Request;

class FormatDetectorTest extends TestCase
{
    /**
     * @var Request\FormatDetector
     */
    private $subject;

    protected function setUp()
    {
        $this->subject = new Request\FormatDetector(['fixture/format' => 'fixture_format'], 'default_format');
    }

    /**
     * @dataProvider getFormatDataProvider
     */
    public function testGetFormat($contentType, $expectedResult)
    {
        /** @var ServerRequestInterface|MockObject $request */
        $request = $this->getMock(ServerRequestInterface::class, [], [], '', false);
        $request->expects($this->once())->method('getHeaderLine')->with('Content-Type')->willReturn($contentType);

        $actualResult = $this->subject->getFormat($request);

        $this->assertSame($expectedResult, $actualResult);
    }

    public function getFormatDataProvider()
    {
        return [
            'detected'  => ['fixture/format', 'fixture_format'],
            'default'   => ['unknown', 'default_format'],
        ];
    }
}
