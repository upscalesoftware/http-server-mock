<?php

namespace Upscale\HttpServerMock\Tests;

use PHPUnit_Framework_TestCase as TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Upscale\HttpServerMock\Config;
use Upscale\HttpServerMock\RequestFactory;
use Upscale\HttpServerMock\ResponseFactory;

class ConfigTest extends TestCase
{
    /**
     * Shorthand for the default value of a request field
     */
    const VAL_DEFAULT = RequestFactory::VALUE_INHERITED;

    /**
     * @var Config
     */
    private $subject;

    /**
     * @var Config\Source\SourceInterface|MockObject
     */
    private $configSource;

    /**
     * @var Config\Syntax\ParserInterface|MockObject
     */
    private $configParser;

    /**
     * @var RequestFactory|MockObject
     */
    private $requestFactory;

    /**
     * @var ResponseFactory|MockObject
     */
    private $responseFactory;

    /**
     * @var Config\RuleFactory|MockObject
     */
    private $ruleFactory;

    protected function setUp()
    {
        $this->configSource = $this->getMock(Config\Source\SourceInterface::class, [], [], '', false);
        $this->configParser = $this->getMock(Config\Syntax\ParserInterface::class, [], [], '', false);
        $this->requestFactory = $this->getMock(RequestFactory::class, [], [], '', false);
        $this->responseFactory = $this->getMock(ResponseFactory::class, [], [], '', false);
        $this->ruleFactory = $this->getMock(Config\RuleFactory::class, [], [], '', false);

        $this->subject = new Config(
            $this->configSource, $this->configParser, $this->requestFactory, $this->responseFactory, $this->ruleFactory
        );
    }

    /**
     * @dataProvider getRulesDataProvider
     */
    public function testGetRules(
        array $configParsed, array $requestFactoryCalls, array $responseFactoryCalls, array $ruleFactoryCalls,
        $expectedResult
    ) {
        // Could have been any invariant value, but it would have not reflected relation to the parsed contents
        $configContents = serialize($configParsed);

        $this->configSource->expects($this->once())->method('getContents')->willReturn($configContents);
        $this->configParser->expects($this->once())->method('parse')->with($configContents)->willReturn($configParsed);
        $this->requestFactory->expects($this->any())->method('create')->willReturnMap($requestFactoryCalls);
        $this->responseFactory->expects($this->any())->method('create')->willReturnMap($responseFactoryCalls);
        $this->ruleFactory->expects($this->any())->method('create')->willReturnMap($ruleFactoryCalls);

        $actualResult = $this->subject->getRules();

        $this->assertSame($expectedResult, $actualResult);
    }

    public function getRulesDataProvider()
    {
        $request = $this->getMock(ServerRequestInterface::class, [], [], '', false);
        $response = $this->getMock(ResponseInterface::class, [], [], '', false);
        $rule = $this->getMock(Config\Rule::class, [], [], '', false);

        $requestTwo = $this->getMock(ServerRequestInterface::class, [], [], '', false);
        $responseTwo = $this->getMock(ResponseInterface::class, [], [], '', false);
        $ruleTwo = $this->getMock(Config\Rule::class, [], [], '', false);

        $requestFactoryDefaults = [self::VAL_DEFAULT, self::VAL_DEFAULT, [], self::VAL_DEFAULT, [], [], $request];
        $responseFactoryDefaults = ['', [], 200, ResponseFactory::REASON_AUTO, $response];
        $ruleFactoryDefaults = [$request, $response, null, 0, $rule];

        return [
            'no rules' => [
                [], [], [], [], []
            ],
            'rule: request default, response default' => [
                [[]],
                [$requestFactoryDefaults],
                [$responseFactoryDefaults],
                [$ruleFactoryDefaults],
                [$rule]
            ],
            'rule: request method, response default' => [
                [['request' => ['method' => 'TEST']]],
                [['TEST', self::VAL_DEFAULT, [], self::VAL_DEFAULT, [], [], $request]],
                [$responseFactoryDefaults],
                [$ruleFactoryDefaults],
                [$rule]
            ],
            'rule: request path, response default' => [
                [['request' => ['path' => '/test/resource']]],
                [[self::VAL_DEFAULT, '/test/resource', [], self::VAL_DEFAULT, [], [], $request]],
                [$responseFactoryDefaults],
                [$ruleFactoryDefaults],
                [$rule]
            ],
            'rule: request params, response default' => [
                [['request' => ['params' => ['var' => 'value']]]],
                [[self::VAL_DEFAULT, self::VAL_DEFAULT, ['var' => 'value'], self::VAL_DEFAULT, [], [], $request]],
                [$responseFactoryDefaults],
                [$ruleFactoryDefaults],
                [$rule]
            ],
            'rule: request body, response default' => [
                [['request' => ['body' => 'Request body']]],
                [[self::VAL_DEFAULT, self::VAL_DEFAULT, [], 'Request body', [], [], $request]],
                [$responseFactoryDefaults],
                [$ruleFactoryDefaults],
                [$rule]
            ],
            'rule: request headers, response default' => [
                [['request' => ['headers' => ['X-Test' => __CLASS__]]]],
                [[self::VAL_DEFAULT, self::VAL_DEFAULT, [], self::VAL_DEFAULT, ['x-test' => __CLASS__], [], $request]],
                [$responseFactoryDefaults],
                [$ruleFactoryDefaults],
                [$rule]
            ],
            'rule: request cookies, response default' => [
                [['request' => ['cookies' => ['USER' => '123']]]],
                [[self::VAL_DEFAULT, self::VAL_DEFAULT, [], self::VAL_DEFAULT, [], ['USER' => '123'], $request]],
                [$responseFactoryDefaults],
                [$ruleFactoryDefaults],
                [$rule]
            ],
            'rule: request format, response default' => [
                [['request' => ['format' => 'fixture']]],
                [$requestFactoryDefaults],
                [$responseFactoryDefaults],
                [[$request, $response, 'fixture', 0, $rule]],
                [$rule]
            ],
            'rule: request default, response body' => [
                [['response' => ['body' => 'Test contents']]],
                [$requestFactoryDefaults],
                [['Test contents', [], 200, ResponseFactory::REASON_AUTO, $response]],
                [$ruleFactoryDefaults],
                [$rule]
            ],
            'rule: request default, response headers' => [
                [['response' => ['headers' => ['Content-Type' => 'text/plain']]]],
                [$requestFactoryDefaults],
                [['', ['Content-Type' => 'text/plain'], 200, ResponseFactory::REASON_AUTO, $response]],
                [$ruleFactoryDefaults],
                [$rule]
            ],
            'rule: request default, response status' => [
                [['response' => ['status' => 403]]],
                [$requestFactoryDefaults],
                [['', [], 403, ResponseFactory::REASON_AUTO, $response]],
                [$ruleFactoryDefaults],
                [$rule]
            ],
            'rule: request default, response reason' => [
                [['response' => ['reason' => 'Success']]],
                [$requestFactoryDefaults],
                [['', [], 200, 'Success', $response]],
                [$ruleFactoryDefaults],
                [$rule]
            ],
            'rule: request default, response delay' => [
                [['response' => ['delay' => 2500]]],
                [$requestFactoryDefaults],
                [$responseFactoryDefaults],
                [[$request, $response, null, 2500000, $rule]],
                [$rule]
            ],
            'multiple rules' => [
                [
                    ['request' => ['path' => '/test/resources/1'], 'response' => ['body' => 'Resource ONE']],
                    ['request' => ['path' => '/test/resources/2'], 'response' => ['body' => 'Resource TWO']],
                ],
                [
                    [self::VAL_DEFAULT, '/test/resources/1', [], self::VAL_DEFAULT, [], [], $request],
                    [self::VAL_DEFAULT, '/test/resources/2', [], self::VAL_DEFAULT, [], [], $requestTwo],
                ],
                [
                    ['Resource ONE', [], 200, ResponseFactory::REASON_AUTO, $response],
                    ['Resource TWO', [], 200, ResponseFactory::REASON_AUTO, $responseTwo],
                ],
                [
                    [$request, $response, null, 0, $rule],
                    [$requestTwo, $responseTwo, null, 0, $ruleTwo],
                ],
                [
                    $rule,
                    $ruleTwo,
                ]
            ],
        ];
    }
}
