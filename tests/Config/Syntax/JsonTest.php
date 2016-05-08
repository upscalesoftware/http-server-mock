<?php

namespace Upscale\HttpServerMock\Tests\Config\Syntax;

use PHPUnit_Framework_TestCase as TestCase;
use Upscale\HttpServerMock\Config;

class JsonTest extends TestCase
{
    /**
     * @var Config\Syntax\Json
     */
    private $subject;

    protected function setUp()
    {
        $this->subject = new Config\Syntax\Json();
    }

    /**
     * @dataProvider parseDataProvider
     */
    public function testParse($data, $expectedResult)
    {
        $actualResult = $this->subject->parse($data);

        $this->assertSame($expectedResult, $actualResult);
    }

    public function parseDataProvider()
    {
        return [
            'empty JSON object' => ['{}', []],
            'empty JSON array' => ['[]', []],
            'JSON object' => [
                '{"field1": "value1", "field2": "value2"}',
                ['field1' => 'value1', 'field2' => 'value2']
            ],
            'array of JSON objects' => [
                '[{"id": 1, "name": "obj1"}, {"id": 2, "name": "obj2"}]',
                [['id' => 1, 'name' => 'obj1'], ['id' => 2, 'name' => 'obj2']]
            ],
        ];
    }

    /**
     * @dataProvider parseExceptionDataProvider
     */
    public function testParseException($data, $expectedExceptionMsg)
    {
        $this->setExpectedException(\UnexpectedValueException::class, $expectedExceptionMsg);

        $this->subject->parse($data);
    }

    public function parseExceptionDataProvider()
    {
        return [
            'not a JSON format'     => ['<?xml version="1.0"?><root/>', 'Configuration is not recognized as JSON'],
            'invalid JSON format'   => ['{"id": 1, "name": "obj1", }', 'Configuration is not recognized as JSON'],
            'JSON but not an array' => ['true', 'Configuration is expected to declare a JSON array'],
        ];
    }
}
