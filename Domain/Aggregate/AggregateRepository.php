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

namespace Codefy\Domain\Aggregate;

interface AggregateRepository
{
    /**
     * Loads an aggregate from the given aggregate id.
     *
     * @param AggregateId $aggregateId
     * @return RecordsEvents|null
     * @throws AggregateNotFoundException
     */
    public function loadAggregateRoot(AggregateId $aggregateId): RecordsEvents|null;

    /**
     * Persist an aggregate.
     *
     * @param RecordsEvents $aggregate
     * @return void
     */
    public function saveAggregateRoot(RecordsEvents $aggregate): void;
}
