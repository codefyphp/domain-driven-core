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

use Codefy\Domain\Aggregate\AggregateId;
use DateTimeInterface;

/**
 * Something that happened in the past and that is of importance to the business.
 */
interface DomainEvent
{
    /**
     * The Aggregate this event belongs to.
     */
    public function aggregateId(): AggregateId;

    /**
     * Date the event was recorded on.
     *
     * @return string|DateTimeInterface
     */
    public function recordedAt(): string|DateTimeInterface;

    /**
     * Append event version.
     */
    public function withVersion(int $version): self;

    /**
     * Version of the recorded event.
     *
     * @return int
     */
    public function aggregateVersion(): int;
}
