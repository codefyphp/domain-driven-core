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

namespace Codefy\Traits;

use Codefy\Domain\EventSourcing\DomainEvent;

trait EventProducerAware
{
    protected int $playhead = 0;

    /** @var array $recordedEvents */
    protected array $recordedEvents = [];

    /**
     * Records domain events.
     */
    protected function recordThat(DomainEvent $event): void
    {
        $this->playhead += 1;

        $this->recordedEvents[] = $event->withPlayhead($this->playhead);

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
