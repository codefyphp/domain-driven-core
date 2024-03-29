<?php

/**
 * CodefyPHP
 *
 * @link       https://github.com/codefyphp/domain-driven-core
 * @copyright  2022 Joshua Parker <joshua@joshuaparker.dev>
 * @copyright  2014 Mathias Verraes <mathias@verraes.net>
 * @license    https://opensource.org/licenses/mit-license.php MIT License
 */

declare(strict_types=1);

namespace Codefy\Domain;

use Codefy\Domain\Aggregate\AggregateId;
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
     */
    public function attachToIdentityMap(RecordsEvents $aggregate): void;

    /**
     * Retrieve an aggregate from the map by its aggregate id.
     * @param AggregateId $aggregateId
     * @return RecordsEvents|null
     */
    public function retrieveFromIdentityMap(AggregateId $aggregateId): RecordsEvents|null;
}
