<?php

/**
 * CodefyPHP
 *
 * @link       https://github.com/codefyphp/codefy
 * @copyright  2022
 * @author     Joshua Parker <josh@joshuaparker.blog>
 * @license    https://opensource.org/licenses/mit-license.php MIT License
 *
 * @since      0.1.0
 */

declare(strict_types=1);

namespace Codefy\Domain\EventSourcing;

use BadMethodCallException;
use ReflectionException;

abstract class BaseProjection implements Projection
{
    /**
     * @throws ReflectionException
     */
    public function project(DomainEvent ...$events): void
    {
        foreach ($events as $event) {
            $method = sprintf('projectWhen%s', new EventName($event));

            if (!method_exists(object_or_class: $this, method: $method)) {
                throw new BadMethodCallException(
                    sprintf("There is no event named '%s' that can be projected to '%s'.", $method, static::class)
                );
            }
            $this->$method($event);
        }
    }
}
