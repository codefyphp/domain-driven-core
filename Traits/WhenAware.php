<?php

/**
 * CodefyPHP
 *
 * @link       https://github.com/codefyphp/codefy
 * @copyright  2022 Joshua Parker <josh@joshuaparker.blog>
 * @license    https://opensource.org/licenses/mit-license.php MIT License
 *
 * @since      1.0.0
 */

declare(strict_types=1);

namespace Codefy\Traits;

use BadMethodCallException;
use Codefy\Domain\EventSourcing\DomainEvent;
use Codefy\Domain\EventSourcing\DomainEvents;
use ReflectionClass;

use function method_exists;
use function sprintf;

trait WhenAware
{
    protected function when(DomainEvent $event): void
    {
        $method = sprintf('when%s', (new ReflectionClass($event))->getShortName());

        if (!method_exists($this, $method)) {
            throw new BadMethodCallException(
                sprintf("There is no method named '%s' that can be called in '%s'.", $method, static::class)
            );
        }

        $this->$method($event);
    }

    protected function whenAll(DomainEvents $events): void
    {
        foreach ($events as $event) {
            $this->when($event);
        }
    }
}
