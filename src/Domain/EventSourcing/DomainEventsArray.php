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

namespace Codefy\Domain\EventSourcing;

use ArrayIterator;
use Countable;
use Exception;
use IteratorAggregate;
use RuntimeException;

use function array_filter;
use function array_map;
use function array_merge;
use function array_values;
use function count;
use function iterator_to_array;

/**
 * @template-implements IteratorAggregate<array-key, mixed>
 */
abstract class DomainEventsArray implements Countable, IteratorAggregate
{
    /** @var array<DomainEvent> $events  */
    private array $events;
    /** @var ArrayIterator<int|string, DomainEvent> */
    private ArrayIterator $iterator;

    /**
     * @param array<DomainEvent> $events
     */
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

    /**
     * @param array<DomainEvent> $events
     */
    public static function fromArray(array $events): static
    {
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

    /**
     * @return ArrayIterator
     */
    public function getIterator(): ArrayIterator
    {
        return $this->iterator;
    }

    /**
     * @return array<mixed>
     * @throws Exception
     */
    public function toArray(): array
    {
        return iterator_to_array(iterator: $this->getIterator());
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
