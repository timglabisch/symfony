<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\DependencyInjection\Exception;


use Symfony\Component\DependencyInjection\Definition;

class InvalidLayerException extends \LogicException implements ExceptionInterface
{

    protected $targetDefinition;
    protected $targetArgumentDefinition;

    public function __construct(Definition $targetDefinition, $currentId, Definition $targetArgumentDefinition, $targetArgumentId)
    {
        $this->message = sprintf(
            'Service "%s" with Layers "%s" can\'t depend on Service "%s" with Layers "%s".',
            $currentId,
            implode(', ', $targetDefinition->getLayers()),
            $targetArgumentId,
            implode(', ', $targetArgumentDefinition->getLayers())
        );
    }

} 