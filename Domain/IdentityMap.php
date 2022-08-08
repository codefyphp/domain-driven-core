<?php

/**
 * CodefyPHP
 *
 * @link       https://github.com/codefyphp/codefy
 * @copyright  2022 Joshua Parker <josh@joshuaparker.blog>
 * @copyright  2014 Mathias Verraes <mathias@verraes.net>
 * @license    https://opensource.org/licenses/mit-license.php MIT License
 *
 * @since      1.0.0
 */

declare(strict_types=1);

namespace Codefy\Domain;

use Codefy\Domain\Aggregate\AggregateId;
use Codefy\Domain\Aggregate\MultipleInstancesOfAggregateDetectedException;
use Codefy\Domain\Aggregate\RecordsEvents;

/**
 * Holds unique Aggregate instances in memory, mapped by id.
 * Useful to make sure an Aggregate is not loaded twice.
 */
interface IdentityMap
{
    /**
     * Attach an aggregate to the map.
     *
     * @param RecordsEvents $aggregate
     * @return void
     * @throws MultipleInstancesOfAggregateDetectedException
     */
    public function attach(RecordsEvents $aggregate): void;

    /**
     * Retrieve an aggregate from the map by its aggregate id.
     * @param AggregateId $aggregateId
     * @return RecordsEvents|null
     */
    public function get(AggregateId $aggregateId): RecordsEvents|null;
}
