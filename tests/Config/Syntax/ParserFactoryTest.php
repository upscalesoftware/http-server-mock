<?php

namespace Upscale\HttpServerMock\Tests\Config\Syntax;

use PHPUnit_Framework_TestCase as TestCase;
use Upscale\HttpServerMock\Config;

class ParserFactoryTest extends TestCase
{
    /**
     * @var Config\Syntax\ParserFactory
     */
    private $subject;

    protected function setUp()
    {
        $this->subject = new Config\Syntax\ParserFactory();
    }

    /**
     * @dataProvider createDataProvider
     */
    public function testCreate($type, $expectedResult)
    {
        $actualResult = $this->subject->create($type);

        $this->assertInstanceOf($expectedResult, $actualResult);
    }

    public function createDataProvider()
    {
        return [
            'short type' => ['json', Config\Syntax\Json::class],
            'full class' => [Config\Syntax\Json::class, Config\Syntax\Json::class],
        ];
    }

    /**
     * @dataProvider createExceptionDataProvider
     */
    public function testCreateException($type, $expectedException, $expectedExceptionMsg)
    {
        $this->setExpectedException($expectedException, $expectedExceptionMsg);

        $this->subject->create($type);
    }

    public function createExceptionDataProvider()
    {
        return [
            'unknown short type' => [
                'unknown',
                \InvalidArgumentException::class,
                'Configuration syntax "unknown" is not recognized'
            ],
            'irrelevant full class' => [
                __CLASS__,
                \UnexpectedValueException::class,
                'Implementation of "Upscale\HttpServerMock\Config\Syntax\ParserInterface" is expected'
            ],
        ];
    }
}
