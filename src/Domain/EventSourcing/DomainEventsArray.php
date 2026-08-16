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

use ReflectionException;

use function array_filter;
use function array_map;
use function array_merge;
use function array_values;
use function count;
use function iterator_to_array;

/**
 * @template-implements \IteratorAggregate<array-key, mixed>
 */
abstract class DomainEventsArray implements \Countable, \IteratorAggregate
{
    /** @var array<DomainEvent> $events  */
    private array $events;
    /** @var \ArrayIterator<int|string, DomainEvent> */
    private \ArrayIterator $iterator;

    /**
     * @param array<DomainEvent> $events
     */
    protected function __construct(array $events)
    {
        $this->events = $events;
        $this->iterator = new \ArrayIterator(array: $events);
    }

    final public function count(): int
    {
        return count($this->events);
    }

    /**
     * @throws ReflectionException
     */
    public static function createEmpty(): static
    {
        return self::newCollection([]);
    }

    /**
     * @param array<DomainEvent> $events
     * @return DomainEventsArray
     * @throws ReflectionException
     */
    public static function fromArray(array $events): static
    {
        return self::newCollection(array_values($events));
    }

    /**
     * @throws ReflectionException
     */
    public static function withSingleEvent(DomainEvent $event): static
    {
        return self::newCollection([$event]);
    }

    /**
     * @throws ReflectionException
     */
    public function appendEvent(DomainEvent $event): static
    {
        $events = $this->events;
        $events[] = $event;

        return self::newCollection($events);
    }

    /**
     * @throws ReflectionException
     */
    public function appendEvents(self $more): static
    {
        $events = array_merge($this->events, $more->events);

        return self::newCollection($events);
    }

    /**
     * @return \ArrayIterator
     */
    public function getIterator(): \ArrayIterator
    {
        return $this->iterator;
    }

    /**
     * @return array<mixed>
     * @throws \Exception
     */
    public function toArray(): array
    {
        return iterator_to_array(iterator: $this->getIterator());
    }

    /**
     * @throws ReflectionException
     */
    public function map(callable $callback): static
    {
        $events = array_map(callback: $callback, array: $this->events);

        return static::fromArray(events: $events);
    }

    /**
     * @throws ReflectionException
     */
    public function filter(callable $callback): static
    {
        $events = array_filter(array: $this->events, callback: $callback);

        return static::fromArray(events: $events);
    }

    public function getFirstEvent(): DomainEvent
    {
        if ($this->isEmpty()) {
            throw new \RuntimeException(message: 'Cannot return first event because DomainEvents array is empty.');
        }

        return $this->events[0];
    }

    public function isEmpty(): bool
    {
        return $this->events === [];
    }

    /**
     * Instantiate the late-static collection through reflection so extensions
     * retain the constructor contract used by the existing collection API.
     *
     * @param array<DomainEvent> $events
     * @throws \ReflectionException
     */
    private static function newCollection(array $events): static
    {
        $reflection = new \ReflectionClass(static::class);
        $collection = $reflection->newInstanceWithoutConstructor();
        $constructor = $reflection->getConstructor();

        if (!$constructor instanceof \ReflectionMethod) {
            throw new \LogicException('A domain event collection must define a constructor.');
        }

        $constructor->invoke($collection, $events);

        return $collection;
    }
}
