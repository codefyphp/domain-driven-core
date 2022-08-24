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

use Codefy\Domain\Aggregate\AggregateId;
use Codefy\Domain\Aggregate\AggregateType;
use Codefy\Domain\Metadata;
use DateTimeInterface;
use Qubus\Support\DateTime\QubusDateTimeImmutable;
use Qubus\Support\DateTime\QubusDateTimeZone;

use function Qubus\Support\Helpers\is_null__;

/**
 * Something that happened in the past and that is of importance to the business.
 */
class AggregateChanged implements DomainEvent
{
    public const DATE_FORMAT = 'Y-m-d H:i:s.u';
    public readonly ?array $payload;
    protected ?array $metadata;
    protected ?DateTimeInterface $recordedAt = null;

    private function __construct(AggregateId $aggregateId, ?array $payload, ?array $metadata = [])
    {
        $this->metadata = $metadata;

        $this->setAggregateId(aggregateId: $aggregateId);
        $this->setPlayhead(playhead: $metadata[Metadata::AGGREGATE_PLAYHEAD] ?? 1);
        $this->setPayload(payload: $payload);
        $this->setEventId(eventId: $metadata[Metadata::EVENT_ID] ?? new EventId());
        $this->setEventType(
            eventType: $metadata[Metadata::EVENT_TYPE] ?? AggregateType::fromClassName(className: static::class)
        );
        $this->init();
    }

    /**
     * Named constructor for generating a domain event.
     */
    final public static function occur(AggregateId $aggregateId, array $payload, array $metadata = []): self
    {
        return new static(aggregateId: $aggregateId, payload: $payload, metadata: $metadata);
    }

    /**
     * Named constructor for generating a domain event from an array.
     */
    final public static function fromArray(array $data): DomainEvent
    {
        return new static(
            aggregateId: $data['aggregateId'],
            payload: $data['payload'] ?? [],
            metadata: $data['metadata'] ?? []
        );
    }

    /**
     * Returns payload array.
     */
    public function payload(): array
    {
        return $this->payload;
    }

    /**
     * Name of the event.
     */
    public function eventType(): string
    {
        return $this->metadata[Metadata::EVENT_TYPE];
    }

    /** {@inheritDoc} */
    public function aggregateId(): AggregateId
    {
        return $this->metadata[Metadata::AGGREGATE_ID];
    }

    /**
     * Uuid of the event.
     */
    public function eventId(): string|EventId
    {
        return $this->metadata[Metadata::EVENT_ID];
    }

    /**
     * Event metadata.
     *
     * @return array
     */
    public function metadata(): array
    {
        return $this->metadata;
    }

    /**
     * Aggregate version.
     */
    public function playhead(): int
    {
        return $this->metadata[Metadata::AGGREGATE_PLAYHEAD];
    }

    /**
     * The date the event occurred on.
     */
    public function recordedAt(): string|DateTimeInterface
    {
        return $this->recordedAt;
    }

    /**
     * Returns array of event data.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'eventId' => $this->eventId(),
            'eventType' => $this->eventType(),
            'recordedAt' => $this->recordedAt(),
            'metadata' => $this->metadata(),
            'payload' => $this->payload(),
        ];
    }

    /**
     * Retrieve data from payload by name.
     */
    public function param(string $name, mixed $default = null): mixed
    {
        return $this->payload()[$name] ?? $default;
    }

    /**
     * Retrieve meta data from metadata by name.
     */
    public function metaParam(string $name, mixed $default = null): mixed
    {
        return $this->metadata()[$name] ?? $default;
    }

    /**
     * Append event metadata.
     */
    final public function withMetadata(array $metadata): self
    {
        $event = clone $this;
        $event->metadata = $metadata;

        return $event;
    }

    /**
     * Append event metadata.
     */
    final public function withAddedMetadata(string $key, mixed $value): self
    {
        $event = clone $this;
        $event->metadata[$key] = $value;

        return $event;
    }

    /**
     * Append event version.
     */
    final public function withPlayhead(int $playhead): self
    {
        $event = clone $this;
        $event->setPlayhead(playhead: $playhead);

        return $event;
    }

    private function setEventId(EventId $eventId): void
    {
        $this->metadata[Metadata::EVENT_ID] = $eventId;
    }

    private function setEventType(string $eventType): void
    {
        $this->metadata[Metadata::EVENT_TYPE] = $eventType;
    }

    private function setAggregateId(AggregateId $aggregateId): void
    {
        $this->metadata[Metadata::AGGREGATE_ID] = $aggregateId;
    }

    private function setPlayhead(int $playhead): void
    {
        $this->metadata[Metadata::AGGREGATE_PLAYHEAD] = $playhead;
    }

    private function setPayload(?array $payload): void
    {
        $this->payload = $payload;
    }

    private function init(): void
    {
        if (is_null__($this->recordedAt)) {
            $this->recordedAt = $this->metadata[Metadata::RECORDED_AT] = new QubusDateTimeImmutable(
                time: 'now',
                tz: new QubusDateTimeZone(timezone: 'UTC')
            );
        }
    }
}
