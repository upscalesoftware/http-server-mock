<?php

namespace Upscale\HttpServerMock\Tests\Body;

use PHPUnit_Framework_TestCase as TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Upscale\HttpServerMock\Body;

class FormatterRegistryTest extends TestCase
{
    /**
     * @var Body\FormatterRegistry
     */
    private $subject;

    /**
     * @var
     */
    private $formatter;

    protected function setUp()
    {
        $this->formatter = $this->getMock(Body\FormatterInterface::class, [], [], '', false);

        $this->subject = new Body\FormatterRegistry(['fixture' => $this->formatter]);
    }

    public function testAddGet()
    {
        /** @var Body\FormatterInterface|MockObject $formatter */
        $formatter = $this->getMock(Body\FormatterInterface::class, [], [], '', false);

        $this->subject->add('test', $formatter);

        $actualResultOne = $this->subject->get('test');
        $actualResultTwo = $this->subject->get('fixture');

        $this->assertSame($formatter, $actualResultOne);
        $this->assertSame($this->formatter, $actualResultTwo);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Format 'fixture' has already been registered
     */
    public function testAddException()
    {
        /** @var Body\FormatterInterface|MockObject $formatter */
        $formatter = $this->getMock(Body\FormatterInterface::class, [], [], '', false);

        $this->subject->add('fixture', $formatter);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Format 'unknown' is not recognized
     */
    public function testGetException()
    {
        $this->subject->get('unknown');
    }
}
