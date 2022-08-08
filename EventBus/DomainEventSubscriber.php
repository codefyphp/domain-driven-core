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

namespace Codefy\EventBus;

use Codefy\Domain\EventSourcing\DomainEvent;

interface DomainEventSubscriber
{
    /**
     * Checks whether the subscriber handles the given domain event.
     *
     * @param DomainEvent $event
     * @return bool
     */
    public function isSubscribedTo(DomainEvent $event): bool;
}
