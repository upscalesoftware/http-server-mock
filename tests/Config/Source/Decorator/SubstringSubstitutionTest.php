<?php

namespace Upscale\HttpServerMock\Tests\Config\Source;

use PHPUnit_Framework_TestCase as TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Upscale\HttpServerMock\Config;

class SubstringSubstitutionTest extends TestCase
{
    /**
     * @var Config\Source\Decorator\SubstringSubstitution
     */
    private $subject;

    /**
     * @var Config\Source\SourceInterface|MockObject
     */
    private $source;

    /**
     * @var array
     */
    private $fixtureMap = [
        '%name%' => 'John Doe',
        '%email%' => 'john.doe@example.com',
    ];

    protected function setUp()
    {
        $this->source = $this->getMock(Config\Source\SourceInterface::class, [], [], '', false);

        $this->subject = new Config\Source\Decorator\SubstringSubstitution($this->source, $this->fixtureMap);
    }

    /**
     * @dataProvider getContentsDataProvider
     */
    public function testGetContents($contents, $expectedResult)
    {
        $this->source->expects($this->once())->method('getContents')->willReturn($contents);

        $actualResult = $this->subject->getContents();

        $this->assertSame($expectedResult, $actualResult);
    }

    public function getContentsDataProvider()
    {
        return [
            'empty' => ['', ''],
            'no substitutions' => [
                'Static contents',
                'Static contents'
            ],
            'one substitution' => [
                'Hello %name%!',
                'Hello John Doe!'
            ],
            'multiple substitutions' => [
                'Hi %name%, your e-mail is %email%',
                'Hi John Doe, your e-mail is john.doe@example.com'
            ],
            'multiple occurrences' => [
                '<a href="mailto:%email%">%email%</a>',
                '<a href="mailto:john.doe@example.com">john.doe@example.com</a>'
            ],
        ];
    }
}
