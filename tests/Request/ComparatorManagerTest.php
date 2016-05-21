<?php

namespace Upscale\HttpServerMock\Tests\Request;

use PHPUnit_Framework_TestCase as TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Upscale\HttpServerMock\Body\FormatterInterface;
use Upscale\HttpServerMock\Body\FormatterRegistry;
use Upscale\HttpServerMock\Request;

class ComparatorManagerTest extends TestCase
{
    /**
     * @var Request\ComparatorManager
     */
    private $subject;

    /**
     * @var FormatterRegistry|MockObject
     */
    private $formatterRegistry;

    protected function setUp()
    {
        $this->formatterRegistry = $this->getMock(FormatterRegistry::class, [], [], '', false);

        $this->subject = new Request\ComparatorManager($this->formatterRegistry);
    }

    public function testGetComparator()
    {
        $formatterOne = $this->getMock(FormatterInterface::class, [], [], '', false);
        $formatterTwo = $this->getMock(FormatterInterface::class, [], [], '', false);

        $this->formatterRegistry
            ->expects($this->exactly(2))
            ->method('get')
            ->willReturnMap([
                ['format_one', $formatterOne],
                ['format_two', $formatterTwo],
            ]);

        /** @var Request\Comparator\Generic $actualResultOne */
        $actualResultOne = $this->subject->getComparator('format_one');

        $this->assertInstanceOf(Request\Comparator\Generic::class, $actualResultOne);
        $this->assertSame($formatterOne, $actualResultOne->getFormatter());

        /** @var Request\Comparator\Generic $actualResultTwo */
        $actualResultTwo = $this->subject->getComparator('format_two');

        $this->assertSame($actualResultOne, $actualResultTwo, 'Same flyweight instance is expected');
        $this->assertSame($formatterTwo, $actualResultTwo->getFormatter());
    }
}
