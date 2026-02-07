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

/**
 * Something that happened in the past and that is of importance to the business.
 */
interface DomainEvent
{
    /**
     * The ID of the Aggregate this event belongs to.
     */
    public function aggregateId(): AggregateId;

    /**
     * Date the event was recorded on.
     *
     * @return ?\DateTimeInterface
     */
    public function recordedAt(): ?\DateTimeInterface;

    /**
     * Append event version.
     */
    public function withPlayhead(int $playhead): self;

    /**
     * Version of the recorded event.
     *
     * @return int
     */
    public function playhead(): int;

    /**
     * Returns payload array.
     *
     * @return array<mixed>
     */
    public function payload(): array;

    /**
     * Name of the event.
     */
    public function eventType(): string;

    /**
     * Uuid of the event.
     */
    public function eventId(): EventId;

    /**
     * Event metadata.
     *
     * @return array<mixed>
     */
    public function metadata(): array;

    /**
     * Retrieve meta data from metadata by name.
     */
    public function metaParam(string $name, mixed $default = null): mixed;
}
