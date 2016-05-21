<?php

namespace Upscale\HttpServerMock\Tests;

use PHPUnit_Framework_TestCase as TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Upscale\HttpServerMock\App;
use Upscale\HttpServerMock\Config;
use Upscale\HttpServerMock\Request\ComparatorInterface;
use Upscale\HttpServerMock\Request\ComparatorManager;
use Upscale\HttpServerMock\Request\FormatDetector;

class AppTest extends TestCase
{
    /**
     * @var App
     */
    private $subject;

    /**
     * @var Config|MockObject
     */
    private $config;

    /**
     * @var FormatDetector|MockObject
     */
    private $formatDetector;

    /**
     * @var ComparatorManager|MockObject
     */
    private $comparatorManager;

    /**
     * @var ComparatorInterface|MockObject
     */
    private $comparator;

    /**
     * @var ServerRequestInterface|MockObject
     */
    private $request;

    protected function setUp()
    {
        $this->config = $this->getMock(Config::class, [], [], '', false);

        $this->request = $this->getMock(ServerRequestInterface::class, [], [], '', false);

        $this->formatDetector = $this->getMock(FormatDetector::class, [], [], '', false);
        $this->formatDetector
            ->expects($this->once())
            ->method('getFormat')
            ->with($this->identicalTo($this->request))
            ->willReturn('fixture');

        $this->comparator = $this->getMock(ComparatorInterface::class, [], [], '', false);
        $this->comparatorManager = $this->getMock(ComparatorManager::class, [], [], '', false);
        $this->comparatorManager->expects($this->atLeastOnce())->method('getComparator')->willReturn($this->comparator);

        $this->subject = new App($this->config, $this->formatDetector, $this->comparatorManager);
    }

    /**
     * @dataProvider handleDataProvider
     */
    public function testHandle(array $rules, array $ruleMatches, $expectedResult)
    {
        $this->config->expects($this->once())->method('getRules')->willReturn($rules);

        $this->comparator
            ->expects($this->atLeastOnce())
            ->method('isEqual')
            ->with($this->identicalTo($this->request), $this->isInstanceOf(ServerRequestInterface::class))
            ->will(call_user_func_array([$this, 'onConsecutiveCalls'], $ruleMatches));

        $actualResult = $this->subject->handle($this->request);

        $this->assertSame($expectedResult, $actualResult);
    }

    public function handleDataProvider()
    {
        $requestOne = $this->getMock(ServerRequestInterface::class, [], [], '', false);
        $requestTwo = $this->getMock(ServerRequestInterface::class, [], [], '', false);

        $responseOne = $this->getMock(ResponseInterface::class, [], [], '', false);
        $responseTwo = $this->getMock(ResponseInterface::class, [], [], '', false);

        $ruleOne = $this->getMock(Config\Rule::class, [], [], '', false);
        $ruleOne->expects($this->any())->method('getRequest')->willReturn($requestOne);
        $ruleOne->expects($this->any())->method('getResponse')->willReturn($responseOne);

        $ruleTwo = $this->getMock(Config\Rule::class, [], [], '', false);
        $ruleTwo->expects($this->any())->method('getRequest')->willReturn($requestTwo);
        $ruleTwo->expects($this->any())->method('getResponse')->willReturn($responseTwo);

        return [
            'mismatch & mismatch'   => [[$ruleOne, $ruleTwo], [false, false], null],
            'match & mismatch'      => [[$ruleOne, $ruleTwo], [true, false], $responseOne],
            'mismatch & match'      => [[$ruleOne, $ruleTwo], [false, true], $responseTwo],
            'match & match'         => [[$ruleOne, $ruleTwo], [true, true], $responseOne],
        ];
    }

    /**
     * @dataProvider handleFormatDataProvider
     */
    public function testHandleFormat($format, $expectedFormat)
    {
        $request = $this->getMock(ServerRequestInterface::class, [], [], '', false);

        $response = $this->getMock(ResponseInterface::class, [], [], '', false);

        $rule = $this->getMock(Config\Rule::class, [], [], '', false);
        $rule->expects($this->once())->method('getRequest')->willReturn($request);
        $rule->expects($this->once())->method('getRequestFormat')->willReturn($format);
        $rule->expects($this->once())->method('getResponse')->willReturn($response);

        $this->config->expects($this->once())->method('getRules')->willReturn([$rule]);

        $this->comparator
            ->expects($this->once())
            ->method('isEqual')
            ->with($this->identicalTo($this->request), $this->identicalTo($request))
            ->willReturn($response);

        $this->comparatorManager
            ->expects($this->once())
            ->method('getComparator')
            ->with($expectedFormat)
            ->willReturn($this->comparator);

        $actualResult = $this->subject->handle($this->request);

        $this->assertSame($response, $actualResult);
    }

    public function handleFormatDataProvider()
    {
        return [
            'detected'  => [null, 'fixture'],
            'explicit'  => ['custom', 'custom'],
        ];
    }

    /**
     * @dataProvider idleDataProvider
     * @group slow
     */
    public function testIdle($delay, $expectedMinDuration, $expectedMaxDuration)
    {
        $request = $this->getMock(ServerRequestInterface::class, [], [], '', false);

        $response = $this->getMock(ResponseInterface::class, [], [], '', false);

        $rule = $this->getMock(Config\Rule::class, [], [], '', false);
        $rule->expects($this->once())->method('getRequest')->willReturn($request);
        $rule->expects($this->once())->method('getResponse')->willReturn($response);
        $rule->expects($this->once())->method('getResponseDelay')->willReturn($delay);

        $this->config->expects($this->once())->method('getRules')->willReturn([$rule]);

        $this->comparator
            ->expects($this->once())
            ->method('isEqual')
            ->with($this->identicalTo($this->request), $this->identicalTo($request))
            ->willReturn($response);

        $timer = new \PHP_Timer();
        $timer->start();
        $actualResult = $this->subject->handle($this->request);
        $actualDuration = $timer->stop();

        $this->assertSame($response, $actualResult);

        $this->assertGreaterThan($expectedMinDuration, $actualDuration, 'Execution time exceeds expectation');
        $this->assertLessThan($expectedMaxDuration, $actualDuration, 'Execution time falls short of expectation');
    }

    public function idleDataProvider()
    {
        return [
            '0.5 sec' => [500000,  0.5, 0.6],
            '1.5 sec' => [1500000, 1.5, 1.6],
        ];
    }
}
