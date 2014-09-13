<?php

namespace Symfony\Component\DependencyInjection\Tests\LayerRule;


use Symfony\Component\DependencyInjection\LayerRule\LayerRuleBuilder;

class LayerRuleBuilderTest extends \PHPUnit_Framework_TestCase
{
    public function testBuilder()
    {
        $layerRuleBuilder = new LayerRuleBuilder();

        $layerRuleBuilder
            ->child('BundleA')
                ->when('"SomeBundleA" in layers')
                ->layerCanDependOn('domain', 'infrastructure')
            ->end()
            ->child('BundleB')
                ->when('"SomeBundleA" in layers')
                ->when('"SomeOtherCondition1" in layers')
                ->layerCanDependOn('domain', 'infrastructure')
                ->layerCanDependOn('controller', 'domain')
            ->end()
            ->child('BundleB')
                ->when('"SomeOtherCondition2" in layers')
                ->layerCanDependOn('controller', 'session')
            ->end()
            ->layerCanDependOn('foo1', 'bar1')
            ->assertLast();
        ;

    }
}
 