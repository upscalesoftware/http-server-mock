<?php

namespace Upscale\HttpServerMock\Tests\Body\Formatter;

use PHPUnit_Framework_TestCase as TestCase;
use Upscale\HttpServerMock\Body\Formatter;

class JsonTest extends TestCase
{
    /**
     * @var Formatter\Json
     */
    private $subject;

    protected function setUp()
    {
        $this->subject = new Formatter\Json();
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
                '[
                    {
                        "id" : 1,
                        "optional" : "value",
                        "flag" : true
                    },
                    {
                        "id" : 2,
                        "flag" : false
                    }
                ]',
                '[{"id":1,"optional":"value","flag":true},{"id":2,"flag":false}]',
            ],
            'invalid' => [
                ' { "field" : "value", } ',
                '{ "field" : "value", }'
            ],
        ];
    }
}
