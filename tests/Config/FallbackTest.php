<?php

namespace Upscale\HttpServerMock\Tests\Config;

use PHPUnit_Framework_TestCase as TestCase;
use Upscale\HttpServerMock\Config;

class FallbackTest extends TestCase
{
    /**
     * @var Config\Fallback
     */
    private $subject;

    protected function setUp()
    {
        $this->subject = new Config\Fallback();
    }

    /**
     * @dataProvider getFirstReadableStreamDataProvider
     */
    public function testGetFirstReadableStream(array $streams, $expectedContents)
    {
        $actualResult = $this->subject->getFirstReadableStream($streams);
        $this->assertSame($expectedContents, $actualResult->getContents());
    }

    public function getFirstReadableStreamDataProvider()
    {
        $streamContents = 'Stream contents';
        $stream = 'data://text/plain;base64,U3RyZWFtIGNvbnRlbnRz';
        $unknownFile = 'non-existent.txt';
        $unknownPhar = 'phar:://non-existent.phar';
        return [
            'unreadable, readable'              => [[$unknownFile, $stream],                $streamContents],
            'readable, unreadable'              => [[$stream, $unknownFile],                $streamContents],
            'readable, readable'                => [[$stream, __FILE__],                    $streamContents],
            'unreadable, unreadable, readable'  => [[$unknownFile, $unknownPhar, $stream],  $streamContents],
            'unreadable, readable, unreadable'  => [[$unknownFile, $stream, $unknownPhar],  $streamContents],
            'unreadable, readable, readable'    => [[$unknownFile, $stream, __FILE__],      $streamContents],
            'readable, unreadable, unreadable'  => [[$stream, $unknownFile, $unknownPhar],  $streamContents],
            'readable, unreadable, readable'    => [[$stream, $unknownFile, __FILE__],      $streamContents],
        ];
    }

    /**
     * @dataProvider getFirstReadableStreamExceptionDataProvider
     */
    public function testGetFirstReadableStreamException(array $streams, $expectedExceptionMsg)
    {
        $this->setExpectedException(\RuntimeException::class, $expectedExceptionMsg);
        $this->subject->getFirstReadableStream($streams);
    }

    public function getFirstReadableStreamExceptionDataProvider()
    {
        $unknownFile = 'path/to/non-existent.txt';
        $invalidStream = 'php:://invalid';
        return [
            'single stream' => [
                [$unknownFile],
                'Unable to locate a readable stream amongst: path/to/non-existent.txt',
            ],
            'multiple streams' => [
                [$unknownFile, $invalidStream],
                'Unable to locate a readable stream amongst: path/to/non-existent.txt, php:://invalid',
            ],
        ];
    }
}
