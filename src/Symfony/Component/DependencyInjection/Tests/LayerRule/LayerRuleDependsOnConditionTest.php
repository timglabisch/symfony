<?php

namespace Symfony\Component\DependencyInjection\Tests\LayerRule;


use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\LayerRule\LayerRuleDependsOnCondition;

class LayerRuleDependsOnConditionTest extends \PHPUnit_Framework_TestCase
{

    public function validDependsOnConditionDataProvider()
    {
        return array(
            array(
                array('a'),
                array('b'),
                new LayerRuleDependsOnCondition('a', 'b')
            ),
            array(
                array('?', 'a'),
                array('??', 'b'),
                new LayerRuleDependsOnCondition('a', 'b')
            )
        );
    }

    /**
     * @dataProvider validDependsOnConditionDataProvider
     */
    public function testValidDependsOnCondition($targetLayers, $argumentLayers, $layerRuleDepdendsOn)
    {
        $targetDefinition = (new Definition())->setLayers($targetLayers);
        $targetArgumentDefinition = (new Definition())->setLayers($argumentLayers);

        $this->assertTrue($layerRuleDepdendsOn->isSatisfied($targetDefinition, $targetArgumentDefinition));
    }

    public function inValidDependsOnConditionDataProvider()
    {
        return array(
            array(
                array('a'),
                array('b'),
                new LayerRuleDependsOnCondition('b', 'a')
            ),
            array(
                array('a'),
                array('b'),
                new LayerRuleDependsOnCondition('a', 'c')
            )
        );
    }

    /**
     * @dataProvider inValidDependsOnConditionDataProvider
     */
    public function testInValidDependsOnCondition($targetLayers, $argumentLayers, $layerRuleDepdendsOn)
    {
        $targetDefinition = (new Definition())->setLayers($targetLayers);
        $targetArgumentDefinition = (new Definition())->setLayers($argumentLayers);

        $this->assertFalse($layerRuleDepdendsOn->isSatisfied($targetDefinition, $targetArgumentDefinition));
    }

}
 