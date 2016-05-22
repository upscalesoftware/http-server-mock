<?php

namespace Upscale\HttpServerMock\Tests\Body\Formatter;

use PHPUnit_Framework_TestCase as TestCase;
use Upscale\HttpServerMock\Body\Formatter;

class XmlTest extends TestCase
{
    /**
     * @var Formatter\Xml
     */
    private $subject;

    protected function setUp()
    {
        $this->subject = new Formatter\Xml();
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
                file_get_contents(__DIR__ . '/_files/source.xml'),
                file_get_contents(__DIR__ . '/_files/normalized.xml'),
            ],
            'invalid' => [
                ' <node attr="value" ',
                '<node attr="value"'
            ],
        ];
    }
}
