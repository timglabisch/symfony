<?php

namespace Symfony\Component\DependencyInjection\Tests\LayerRule;


use Symfony\Component\DependencyInjection\LayerRule\ExpressionLayerRule;
use Symfony\Component\ExpressionLanguage\Expression;

class ExpressionLayerRuleTest extends \PHPUnit_Framework_TestCase {

    protected function getMockCondition($returns) {
        $condition = $this->getMock('Symfony\Component\DependencyInjection\LayerRule\LayerRuleConditionInterface');
        $condition->expects($this->any())
            ->method('isSatisfied')
            ->will($this->returnValue($returns));

        return $condition;
    }

    public function ExpressionLayerRuleConditionDataProvider()
    {
        return array(
            array(array($this->getMockCondition(false)), false),
            array(array($this->getMockCondition(true)), true),
            array(array($this->getMockCondition(false), $this->getMockCondition(true)), true)
        );
    }

    /**
     * @dataProvider ExpressionLayerRuleConditionDataProvider
     */
    public function testExpressionLayerRuleCondition($conditions, $returnValue)
    {
        $mockDefinition = $this->getMock('Symfony\Component\DependencyInjection\Definition');
        $mockDefinitionArgument = $this->getMock('Symfony\Component\DependencyInjection\Definition');

        $layerRule = new ExpressionLayerRule($conditions);
        $this->assertEquals($layerRule->isValid($mockDefinition, $mockDefinitionArgument), $returnValue);
    }

    public function ExpressionLayerRuleDataProvider()
    {
        return array(
            array(array(new Expression('"a" in layers')), array('b'), false),
            array(array(new Expression('"a" in layers')), array('a'), true),
            array(array(new Expression('"a" in layers')), array('a', 'b'), true),
            array(array(new Expression('"b" in layers')), array('a', 'b'), true),
            array(array(new Expression('"b" in layers and "a" in layers')), array('a', 'b'), true),
            array(array(new Expression('"b" in layers'), new Expression('"a" in layers')), array('a', 'b'), true),
            array(array(new Expression('"b" in layers'), new Expression('"c" in layers')), array('a', 'b'), true),
            array(array(new Expression('"b" in layers'), new Expression('"c" in layers')), array('a', 'c'), true),
            array(array(new Expression('"b" in layers'), new Expression('"c" in layers')), array('x', 'y'), false),
        );
    }

    /**
     * @dataProvider ExpressionLayerRuleDataProvider
     */
    public function testExpressionLayerWhen($expression, $layers, $returnValue)
    {
        $mockDefinition = $this->getMock('Symfony\Component\DependencyInjection\Definition');
        $mockDefinition->expects($this->any())
            ->method('getLayers')
            ->will($this->returnValue($layers))
        ;

        $mockDefinitionArgument = $this->getMock('Symfony\Component\DependencyInjection\Definition');

        $layerRule = new ExpressionLayerRule(array($this->getMockCondition(true)), $expression);
        $this->assertEquals($layerRule->isValid($mockDefinition, $mockDefinitionArgument), $returnValue);
    }

    public function testChildren()
    {
        $mockDefinition = $this->getMock('Symfony\Component\DependencyInjection\Definition');
        $mockDefinitionArgument = $this->getMock('Symfony\Component\DependencyInjection\Definition');

        $mockLayerRule = $this->getMockBuilder('Symfony\Component\DependencyInjection\LayerRule\ExpressionLayerRule')
            ->disableOriginalConstructor()
            ->getMock();

        $mockLayerRule->expects($this->any())
            ->method('isValid')
            ->will($this->returnValue(true))
        ;

        $layerRule = new ExpressionLayerRule(array(), array(), array($mockLayerRule));
        $this->assertTrue($layerRule->isValid($mockDefinition, $mockDefinitionArgument));
    }

    public function testMultiple()
    {
        $mockDefinition = $this->getMock('Symfony\Component\DependencyInjection\Definition');
        $mockDefinitionArgument = $this->getMock('Symfony\Component\DependencyInjection\Definition');

        $mockLayerRule1 = $this->getMockBuilder('Symfony\Component\DependencyInjection\LayerRule\ExpressionLayerRule')
            ->disableOriginalConstructor()
            ->getMock();

        $mockLayerRule1->expects($this->any())
            ->method('isValid')
            ->will($this->returnValue(true))
        ;

        $mockLayerRule2 = $this->getMockBuilder('Symfony\Component\DependencyInjection\LayerRule\ExpressionLayerRule')
            ->disableOriginalConstructor()
            ->getMock();

        $mockLayerRule2->expects($this->any())
            ->method('isValid')
            ->will($this->returnValue(true))
        ;

        $layerRule = new ExpressionLayerRule(array(), array(), array($mockLayerRule1, $mockLayerRule2));
        $this->assertTrue($layerRule->isValid($mockDefinition, $mockDefinitionArgument));
    }

    public function testMultipleFalse()
    {
        $mockDefinition = $this->getMock('Symfony\Component\DependencyInjection\Definition');
        $mockDefinitionArgument = $this->getMock('Symfony\Component\DependencyInjection\Definition');

        $mockLayerRule1 = $this->getMockBuilder('Symfony\Component\DependencyInjection\LayerRule\ExpressionLayerRule')
            ->disableOriginalConstructor()
            ->getMock();

        $mockLayerRule1->expects($this->any())
            ->method('isValid')
            ->will($this->returnValue(true))
        ;

        $mockLayerRule2 = $this->getMockBuilder('Symfony\Component\DependencyInjection\LayerRule\ExpressionLayerRule')
            ->disableOriginalConstructor()
            ->getMock();

        $mockLayerRule2->expects($this->any())
            ->method('isValid')
            ->will($this->returnValue(false))
        ;

        $layerRule = new ExpressionLayerRule(array(), array(), array($mockLayerRule1, $mockLayerRule2));
        $this->assertTrue($layerRule->isValid($mockDefinition, $mockDefinitionArgument));
    }

}
 