<?php

namespace Symfony\Component\DependencyInjection\LayerRule;


use Symfony\Component\DependencyInjection\Definition;

interface LayerRuleInterface {

    public function isValid(Definition $target, Definition $argument);

} 