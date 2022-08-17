<?php

/**
 * CodefyPHP
 *
 * @link       https://github.com/codefyphp/codefy
 * @copyright  2022 Joshua Parker <josh@joshuaparker.blog>
 * @copyright  2014 Mathias Verraes <mathias@verraes.net>
 * @license    https://opensource.org/licenses/mit-license.php MIT License
 *
 * @since      0.1.0
 */

declare(strict_types=1);

namespace Codefy\Domain\Aggregate;

use Codefy\Domain\EventSourcing\DomainEvents;

/**
 * An object that records the events that happened to it
 * since the last time it was cleared, or since it was
 * restored from persistence.
 */
interface RecordsEvents
{
    /**
     * Returns unique aggregate id.
     */
    public function aggregateId(): AggregateId;

    /**
     * Determine whether the object's state has changed since the last clearRecordedEvent();
     */
    public function hasRecordedEvents(): bool;

    /**
     * Get all the Domain Events that were recorded since the last time it was cleared, or since it was
     * restored from persistence. This does not include events that were recorded prior.
     */
    public function getRecordedEvents(): DomainEvents;

    /**
     * Clears the record of new Domain Events. This doesn't clear the history of the object.
     */
    public function clearRecordedEvents(): void;
}
