<?php

namespace Upscale\HttpServerMock\Tests\Body\Formatter;

use PHPUnit_Framework_TestCase as TestCase;
use Upscale\HttpServerMock\Body\Formatter;

class HtmlTest extends TestCase
{
    /**
     * @var Formatter\Html
     */
    private $subject;

    protected function setUp()
    {
        $this->subject = new Formatter\Html();
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
            'empty' => ['', ''],
            'valid' => [
                file_get_contents(__DIR__ . '/_files/source.html'),
                file_get_contents(__DIR__ . '/_files/normalized.html'),
            ],
            'invalid' => [
                ' <body class="page" ',
                '<html><body class="page"></body></html>' . "\n"
            ],
        ];
    }
}
