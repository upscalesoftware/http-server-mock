<?php

namespace Upscale\HttpServerMock\Tests;

use PHPUnit_Framework_TestCase as TestCase;
use Psr\Http\Message\StreamInterface;
use Upscale\HttpServerMock\StreamFactory;

class StreamFactoryTest extends TestCase
{
    /**
     * @var StreamFactory
     */
    private $subject;

    protected function setUp()
    {
        $this->subject = new StreamFactory();
    }

    /**
     * @dataProvider createDataProvider
     */
    public function testCreate($data, $expectedResult)
    {
        $actualResult = $this->subject->create($data);

        $this->assertInstanceOf(StreamInterface::class, $actualResult);
        $this->assertSame($expectedResult, $actualResult->getContents());
    }

    public function createDataProvider()
    {
        return [
            'raw contents'  => ['Raw contents', 'Raw contents'],
            'data stream'   => ['data://text/plain;base64,U3RyZWFtIGNvbnRlbnRz', 'Stream contents'],
            'file stream'   => ['file://' . __FILE__, file_get_contents(__FILE__)],
        ];
    }
}
