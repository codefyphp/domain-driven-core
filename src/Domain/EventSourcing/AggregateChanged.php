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

use Codefy\Domain\Aggregate\AggregateId;
use Codefy\Domain\Aggregate\AggregateType;
use Codefy\Domain\Metadata;
use Qubus\Support\DateTime\QubusDateTimeImmutable;
use Qubus\Support\DateTime\QubusDateTimeZone;

use function Qubus\Support\Helpers\is_null__;

/**
 * Something that happened in the past and that is of importance to the business.
 */
class AggregateChanged implements DomainEvent
{
    public const string DATE_FORMAT = 'Y-m-d H:i:s.u';
    /** @var array<mixed>|null $payload */
    public ?array $payload = [];
    /** @var array<mixed>|null $metadata */
    protected ?array $metadata = [];
    protected ?\DateTimeInterface $recordedAt = null;

    /**
     * @param AggregateId $aggregateId
     * @param array<mixed>|null $payload
     * @param array<mixed>|null $metadata
     */
    final private function __construct(AggregateId $aggregateId, ?array $payload, ?array $metadata = [])
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
     *
     * @param AggregateId $aggregateId
     * @param array<mixed> $payload
     * @param array<mixed> $metadata
     * @return static
     */
    final public static function occur(AggregateId $aggregateId, array $payload, array $metadata = []): static
    {
        return new static(aggregateId: $aggregateId, payload: $payload, metadata: $metadata);
    }

    /**
     * Named constructor for generating a domain event from an array.
     *
     * @param array<mixed> $data
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
     * {@inheritDoc}
     */
    public function payload(): array
    {
        return $this->payload;
    }

    /**
     * {@inheritDoc}
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
     * {@inheritDoc}
     */
    public function eventId(): EventId
    {
        return $this->metadata[Metadata::EVENT_ID];
    }

    /**
     * {@inheritDoc}
     */
    public function metadata(): array
    {
        return $this->metadata;
    }

    /**
     * {@inheritDoc}
     */
    public function playhead(): int
    {
        return $this->metadata[Metadata::AGGREGATE_PLAYHEAD];
    }

    /**
     * {@inheritDoc}
     */
    public function recordedAt(): ?\DateTimeInterface
    {
        return $this->recordedAt;
    }

    /**
     * Returns array of event data.
     *
     * @return array<string, mixed>
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
     * {@inheritDoc}
     */
    public function metaParam(string $name, mixed $default = null): mixed
    {
        return $this->metadata()[$name] ?? $default;
    }

    /**
     * Append event metadata.
     *
     * @param array<mixed> $metadata
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

    /**
     * @param array<mixed>|null $payload
     * @return void
     */
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
