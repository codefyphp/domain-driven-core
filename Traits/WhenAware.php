<?php

/**
 * CodefyPHP
 *
 * @link       https://github.com/codefyphp/domain-driven-core
 * @copyright  2022
 * @author     Joshua Parker <joshua@joshuaparker.dev>
 * @license    https://opensource.org/licenses/mit-license.php MIT License
 */

declare(strict_types=1);

namespace Codefy\Traits;

use BadMethodCallException;
use Codefy\Domain\EventSourcing\DomainEvent;
use Codefy\Domain\EventSourcing\DomainEvents;
use Codefy\Domain\EventSourcing\EventName;

use function method_exists;
use function sprintf;

trait WhenAware
{
    protected function when(DomainEvent $event): void
    {
        $method = sprintf('when%s', new EventName($event));

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
