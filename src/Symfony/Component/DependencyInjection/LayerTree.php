<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\DependencyInjection;


class LayerTree {

    protected $identifier;

    protected $parent;

    public function __construct($identifier, LayerTree $parent = null)
    {
        $this->identifier = $identifier;
        $this->parent = $parent;
    }

    public function addLayer($identifier) {

    }

    public function end() {
        return $this->parent ? $this->parent : $this;
    }

} 