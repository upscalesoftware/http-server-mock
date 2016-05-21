<?php

namespace Upscale\HttpServerMock\Tests\Body\Formatter;

use PHPUnit_Framework_TestCase as TestCase;
use Upscale\HttpServerMock\Body\Formatter;

class TextTest extends TestCase
{
    /**
     * @var Formatter\Text
     */
    private $subject;

    protected function setUp()
    {
        $this->subject = new Formatter\Text();
    }

    /**
     * @dataProvider normalizeDataProvider
     */
    public function testNormalize($value, $expectedResult)
    {
        $actualResult = $this->subject->normalize($value);

        $this->assertSame($expectedResult, $actualResult);
    }

    public function normalizeDataProvider()
    {
        return [
            'empty'             => ["", ""],
            'CR'                => ["line_\t_1\r\rline_\0_2", "line_\t_1\n\nline_\0_2"],
            'LF'                => ["line_\t_1\n\nline_\0_2", "line_\t_1\n\nline_\0_2"],
            'CRLF'              => ["line_\t_1\r\nline_\0_2", "line_\t_1\nline_\0_2"],
            'LFCR'              => ["line_\t_1\n\rline_\0_2", "line_\t_1\nline_\0_2"],
            'preceding space'   => ["    text", "text"],
            'preceding CR'      => ["\r\rtext", "text"],
            'preceding LF'      => ["\n\ntext", "text"],
            'preceding CRLF'    => ["\r\ntext", "text"],
            'preceding LFCR'    => ["\n\rtext", "text"],
            'preceding tab'     => ["\t\ttext", "text"],
            'preceding v-tab'   => ["\x0Btext", "text"],
            'preceding 0 byte'  => ["\0\0text", "text"],
            'trailing space'    => ["text    ", "text"],
            'trailing CR'       => ["text\r\r", "text"],
            'trailing LF'       => ["text\n\n", "text"],
            'trailing CRLF'     => ["text\r\n", "text"],
            'trailing LFCR'     => ["text\n\r", "text"],
            'trailing tab'      => ["text\t\t", "text"],
            'trailing v-tab'    => ["text\x0B", "text"],
            'trailing 0 byte'   => ["text\0\0", "text"],
        ];
    }
}
