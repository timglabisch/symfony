<?php

namespace Symfony\Component\DependencyInjection\LayerRule;


use Symfony\Component\ExpressionLanguage\Expression;

class LayerRuleBuilder {

    protected $when = array();

    protected $childRules = array();

    protected $conditions = array();

    protected $parent;

    public function __construct(LayerRuleBuilder $parent = null)
    {
        $this->parent = $parent;
    }

    /**
     * @param $when
     * @return $this
     */
    public function when($when)
    {
        $this->when[] = new Expression($when);
        return $this;
    }

    /**
     * @param $name
     * @return $this
     */
    public function child($name = null)
    {
        if ($name === null) {
            $rule = new static($this);
            $this->childRules[] = $rule;
            return $rule;
        }

        if (!isset($this->childRules[$name])) {
            $this->childRules[$name] = new static($this);
        }

        return $this->childRules[$name];
    }

    /**
     * @param LayerRuleBuilder $layerRuleBuilder
     * @return $this
     */
    public function append(LayerRuleBuilder $layerRuleBuilder)
    {
        $this->childRules[] = $layerRuleBuilder;
        return $this;
    }

    /**
     * @return $this
     * @throws \LogicException
     */
    public function end()
    {
        if (!$this->parent) {
            throw new \LogicException();
        }

        return $this->parent;
    }

    public function assertLast()
    {
        if ($this->parent) {
            throw new \LogicException("end call is missing");
        }

        return $this;
    }

    /**
     * @param LayerRuleConditionInterface $condition
     * @return $this
     */
    public function addCondition(LayerRuleConditionInterface $condition)
    {
        $this->conditions[] = $condition;
        return $this;
    }

    /**
     * @param LayerRuleConditionInterface $condition
     * @return $this
     */
    public function layerCanDependOn($layer, $layerDependsOn)
    {
        $this->conditions[] = new LayerRuleDependsOnCondition($layer, $layerDependsOn);
        return $this;
    }

    /**
     * @return ExpressionLayerRule
     */
    public function build()
    {
        return new ExpressionLayerRule($this->conditions, $this->when, array_map(function(LayerRuleBuilder $v) { return $v->build(); }, $this->childRules));
    }

} 