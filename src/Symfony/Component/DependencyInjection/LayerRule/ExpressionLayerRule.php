<?php

namespace Symfony\Component\DependencyInjection\LayerRule;

use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

class ExpressionLayerRule implements LayerRuleInterface {

    /** @var Expression[] */
    protected $when = array();

    /** @var LayerRuleConditionInterface[] */
    protected $coditions = array();

    /** @var LayerRuleInterface[] */
    protected $children = array();

    public function __construct($conditions, $when = array(), $children = array())
    {
        array_map(array($this, 'addCondition'), $conditions);
        array_map(array($this, 'addWhen'), $when);
        array_map(array($this, 'addChildren'), $children);
    }

    private function addWhen(Expression $expression)
    {
        $this->when[] = $expression;
    }

    private function addCondition(LayerRuleConditionInterface $expression)
    {
        $this->coditions[] = $expression;
    }

    private function addChildren(LayerRuleInterface $layerRule)
    {
        $this->children[] = $layerRule;
    }

    public function isValid(Definition $targetDefinition, Definition $argumentDefinition)
    {
        $expressionLanguage = new ExpressionLanguage();

        $context = array(
            'layers' => $targetDefinition->getLayers()
        );

        if (!empty($this->when)) {
            if (!count(array_filter($this->when, function($when) use ($expressionLanguage, $context) {
                return $expressionLanguage->evaluate($when, $context);
            }))) {
                return false;
            }
        }

        foreach ($this->coditions as $condition) {
            if ($condition->isSatisfied($targetDefinition, $argumentDefinition)) {
                return true;
            }
        }

        foreach ($this->children as $child) {
            if ($child->isValid($targetDefinition, $argumentDefinition)) {
                return true;
            }
        }

        if (empty($this->coditions)) {
            return false;
        }

        return false;
    }

} 