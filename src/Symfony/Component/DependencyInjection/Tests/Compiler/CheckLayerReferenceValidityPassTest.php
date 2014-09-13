<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\DependencyInjection\Tests\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CheckReferenceValidityPass;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class CheckLayerReferenceValidityPassTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ContainerBuilder
     */
    protected $containerBuilder;

    public function setUp()
    {
        $this->containerBuilder = new ContainerBuilder();
        $this->containerBuilder->getLayerRuleBuilder()
            ->child('BundleA')
                ->when('"SomeBundleA" in layers')
                ->layerCanDependOn('domain', 'infrastructure')
            ->end()
                ->child('BundleB')
                ->when('"SomeBundleB" in layers')
                ->when('"SomeBundleC" in layers')
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

    public function testBundleA()
    {
        $this->containerBuilder->register('a')->setLayers(array('foo1'))->addArgument(new Reference('b'));
        $this->containerBuilder->register('b')->setLayers(array('bar1'));

        $this->process($this->containerBuilder);
    }

    public function testWhen()
    {
        $this->containerBuilder->register('a')->setLayers(array('SomeBundleA', 'domain'))->addArgument(new Reference('b'));
        $this->containerBuilder->register('b')->setLayers(array('infrastructure'));

        $this->process($this->containerBuilder);
    }

    public function testMultipleWhen()
    {
        $this->containerBuilder->register('a1')->setLayers(array('SomeBundleB', 'domain'))->addArgument(new Reference('b1'));
        $this->containerBuilder->register('b1')->setLayers(array('infrastructure'));

        $this->containerBuilder->register('a2')->setLayers(array('SomeBundleC', 'domain'))->addArgument(new Reference('b2'));
        $this->containerBuilder->register('b2')->setLayers(array('infrastructure'));

        $this->containerBuilder->register('a3')->setLayers(array('SomeBundleB', 'SomeBundleC', 'domain'))->addArgument(new Reference('b3'));
        $this->containerBuilder->register('b3')->setLayers(array('infrastructure'));

        $this->process($this->containerBuilder);
    }

    public function testDeepStructure()
    {
        $containerBuilder = new ContainerBuilder();
        $containerBuilder->getLayerRuleBuilder()
            ->layerCanDependOn('aa', 'bb')
            ->layerCanDependOn('bb', 'cc')
            ->layerCanDependOn('cc', 'dd')
            ->layerCanDependOn('dd', ContainerBuilder::LAYER_DEFAULT)
            ->assertLast();

        $containerBuilder->register('1')->setLayers(array('aa'))->addArgument(new Reference('2'));
        $containerBuilder->register('2')->setLayers(array('bb'))->addArgument(new Reference('3'));
        $containerBuilder->register('3')->setLayers(array('cc'))->addArgument(new Reference('4'));
        $containerBuilder->register('4')->setLayers(array('dd'))->addArgument(new Reference('5'));
        $containerBuilder->register('5');

        $this->process($this->containerBuilder);
    }

    public function testMultipleArguments()
    {
        $containerBuilder = new ContainerBuilder();
        $containerBuilder->getLayerRuleBuilder()
            ->layerCanDependOn('aa', 'bb')
            ->layerCanDependOn('aa', 'cc')
            ->assertLast();

        $containerBuilder->register('1')->setLayers(array('aa'))->addArgument(new Reference('2', '3'));
        $containerBuilder->register('2')->setLayers(array('bb'));
        $containerBuilder->register('3')->setLayers(array('cc'));

        $this->process($this->containerBuilder);
    }

    /**
     * @expectedException \Symfony\Component\DependencyInjection\Exception\InvalidLayerException
     * @expectedExceptionMessage Service "aaaaaa1" with Layers "a" can't depend on Service "aaaaaa2" with Layers "b".
     */
    public function testInvalidReference()
    {
        $containerBuilder = new ContainerBuilder();

        $containerBuilder->register('aaaaaa1')->setLayers(array('a'))->addArgument(new Reference('aaaaaa2'));
        $containerBuilder->register('aaaaaa2')->setLayers(array('b'));

        $this->process($containerBuilder);
    }

    /**
     * @expectedException \Symfony\Component\DependencyInjection\Exception\InvalidLayerException
     * @expectedExceptionMessage Service "aaaaaa1" with Layers "a, b" can't depend on Service "aaaaaa2" with Layers "c, d".
     */
    public function testInvalidReferenceWithMultipleServices()
    {
        $containerBuilder = new ContainerBuilder();

        $containerBuilder->register('aaaaaa1')->setLayers(array('a', 'b'))->addArgument(new Reference('aaaaaa2'));
        $containerBuilder->register('aaaaaa2')->setLayers(array('c', 'd'));

        $this->process($containerBuilder);
    }

    protected function process(ContainerBuilder $container)
    {
        $pass = new CheckReferenceValidityPass();
        $pass->process($container);
    }
}
