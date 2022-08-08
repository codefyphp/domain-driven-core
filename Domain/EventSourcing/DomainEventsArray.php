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

namespace Codefy\Domain\EventSourcing;

use ArrayIterator;
use Countable;
use IteratorAggregate;
use RuntimeException;

use function array_filter;
use function array_map;
use function array_merge;
use function array_values;
use function count;

abstract class DomainEventsArray implements Countable, IteratorAggregate
{
    private array $events;
    private ArrayIterator $iterator;

    protected function __construct(array $events)
    {
        $this->events = $events;
        $this->iterator = new ArrayIterator(array: $events);
    }

    final public function count(): int
    {
        return count($this->events);
    }

    public static function createEmpty(): static
    {
        return new static([]);
    }

    public static function fromArray(array $events): static
    {
        foreach ($events as $event) {
            if (!$event instanceof DomainEvent) {
                throw new DomainEventIsImmutableException(message: 'Only instances of DomainEvent are allowed.');
            }
        }
        return new static(array_values($events));
    }

    public static function withSingleEvent(DomainEvent $event): static
    {
        return new static([$event]);
    }

    public function appendEvent(DomainEvent $event): static
    {
        $events = $this->events;
        $events[] = $event;

        return new static($events);
    }

    public function appendEvents(self $more): static
    {
        $events = array_merge($this->events, $more->events);

        return new static($events);
    }

    public function getIterator(): ArrayIterator
    {
        return $this->iterator;
    }

    public function map(callable $callback): static
    {
        $events = array_map(callback: $callback, array: $this->events);

        return static::fromArray(events: $events);
    }

    public function filter(callable $callback): static
    {
        $events = array_filter(array: $this->events, callback: $callback);

        return static::fromArray(events: $events);
    }

    public function getFirstEvent(): DomainEvent
    {
        if ($this->isEmpty()) {
            throw new RuntimeException(message: 'Cannot return first event because DomainEvents array is empty.');
        }

        return $this->events[0];
    }

    public function isEmpty(): bool
    {
        return $this->events === [];
    }
}
