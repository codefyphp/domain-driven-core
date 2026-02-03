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

namespace Codefy\EventBus;

use Codefy\Domain\EventSourcing\DomainEvent;

interface DomainEventPublisher
{
    /**
     * Publishes a domain event.
     *
     * @param DomainEvent $event
     * @return void
     */
    public function publish(DomainEvent $event): void;
}
