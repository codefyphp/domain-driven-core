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

use Codefy\Domain\EventSourcing\DomainEvent;

trait EventProducerAware
{
    protected int $aggregateVersion = 0;

    /** @var array $recordedEvents */
    protected array $recordedEvents = [];

    /**
     * Records domain events.
     */
    protected function recordThat(DomainEvent $event): void
    {
        $this->aggregateVersion += 1;

        $this->recordedEvents[] = $event->withVersion($this->aggregateVersion);

        $this->when($event);
    }

    public function pullDomainEvents(): array
    {
        $recordedEvents = $this->recordedEvents;
        $this->recordedEvents = [];

        return $recordedEvents;
    }

    abstract protected function when(DomainEvent $event): void;
}
