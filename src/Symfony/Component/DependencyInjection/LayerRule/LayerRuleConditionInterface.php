<?php

namespace Symfony\Component\DependencyInjection\LayerRule;


use Symfony\Component\DependencyInjection\Definition;

interface LayerRuleConditionInterface
{
    public function isSatisfied(Definition $target, Definition $argument);
} 