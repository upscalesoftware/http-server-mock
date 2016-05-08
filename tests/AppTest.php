<?php

namespace Upscale\HttpServerMock\Tests;

use PHPUnit_Framework_TestCase as TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Upscale\HttpServerMock\App;
use Upscale\HttpServerMock\Config;
use Upscale\HttpServerMock\Request\ComparatorInterface;

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
        $this->comparator = $this->getMock(ComparatorInterface::class, [], [], '', false);
        $this->request = $this->getMock(ServerRequestInterface::class, [], [], '', false);

        $this->subject = new App($this->config, $this->comparator);
    }

    /**
     * @dataProvider handleDataProvider
     */
    public function testHandle(array $rules, array $ruleMatches, $expectedResult)
    {
        $this->config->expects($this->once())->method('getRules')->willReturn($rules);

        $this->comparator
            ->expects($this->any())
            ->method('isEqual')
            ->with($this->request, $this->isInstanceOf(ServerRequestInterface::class))
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
            ->with($this->request, $request)
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
