<?php

namespace Upscale\HttpServerMock\Tests\Body\Formatter;

use PHPUnit_Framework_TestCase as TestCase;
use Upscale\HttpServerMock\Body\Formatter;

class BinaryTest extends TestCase
{
    /**
     * @var Formatter\Binary
     */
    private $subject;

    protected function setUp()
    {
        $this->subject = new Formatter\Binary();
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
        $object = new \stdClass();
        $contents = file_get_contents(__FILE__);
        return [
            'passthru'  => [$object, $object],
            'contents'  => [$contents, $contents],
        ];
    }
}
