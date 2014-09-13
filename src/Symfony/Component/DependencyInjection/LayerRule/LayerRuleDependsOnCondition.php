<?php

namespace Symfony\Component\DependencyInjection\LayerRule;


use Symfony\Component\DependencyInjection\Definition;

class LayerRuleDependsOnCondition implements LayerRuleConditionInterface
{

    protected $layer;

    protected $dependsOnLayer;

    public function __construct($layer, $dependsOnLayer)
    {
        $this->layer = $layer;
        $this->dependsOnLayer = $dependsOnLayer;
    }

    public function isSatisfied(Definition $target, Definition $argumentDefiniton)
    {
        if (!in_array($this->layer, $target->getLayers())) {
            return false;
        }

        if (!in_array($this->dependsOnLayer, $argumentDefiniton->getLayers())) {
            return false;
        }

        return true;
    }

} 