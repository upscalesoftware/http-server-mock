<?php

namespace Upscale\HttpServerMock\Tests\Config\Source;

use PHPUnit_Framework_TestCase as TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Psr\Http\Message\StreamInterface;
use Upscale\HttpServerMock\Config;

class StreamTest extends TestCase
{
    /**
     * @var Config\Source\Stream
     */
    private $subject;

    /**
     * @var StreamInterface|MockObject
     */
    private $stream;

    protected function setUp()
    {
        $this->stream = $this->getMock(StreamInterface::class, [], [], '', false);

        $this->subject = new Config\Source\Stream($this->stream);
    }

    public function testGetContents()
    {
        $contents = 'Fixture contents';
        $this->stream->expects($this->once())->method('getContents')->willReturn($contents);

        $actualResult = $this->subject->getContents();

        $this->assertSame($contents, $actualResult);
    }
}
