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

namespace Codefy\Traits;

use Codefy\Domain\Aggregate\AggregateId;
use Codefy\Domain\Aggregate\RecordsEvents;

use function Qubus\Support\Helpers\is_true__;

trait IdentityMapAware
{
    protected array $identityMap = [];
    protected bool $enableIdentityMap = true;

    /**
     * Attach an aggregate to the map.
     *
     * @param RecordsEvents $aggregate
     * @return void
     */
    public function attachToIdentityMap(RecordsEvents $aggregate): void
    {
        if (is_true__($this->enableIdentityMap)) {
            $this->identityMap[$aggregate->aggregateId()->__toString()] = $aggregate;
        }
    }

    /**
     * Retrieve an aggregate from the map by its aggregate id.
     * @param AggregateId $aggregateId
     * @return RecordsEvents|null
     */
    public function retrieveFromIdentityMap(AggregateId $aggregateId): RecordsEvents|null
    {
        if (is_true__($this->enableIdentityMap) && isset($this->identityMap[$aggregateId->__toString()])) {
            return $this->identityMap[$aggregateId->__toString()];
        }

        return null;
    }

    /**
     * Clear the identity map.
     *
     * @return void
     */
    public function clearIdentityMap(): void
    {
        $this->identityMap = [];
    }

    /**
     * Remove aggregate from identity map.
     *
     * @param RecordsEvents $aggregate
     * @return void
     */
    public function removeFromIdentityMap(RecordsEvents $aggregate): void
    {
        if (
            is_true__($this->enableIdentityMap) && isset(
                $this->identityMap[$aggregate->aggregateId()->__toString()]
            )
        ) {
            unset($this->identityMap[$aggregate->aggregateId()->__toString()]);
        }
    }

    /**
     * Set whether identity map is enabled.
     *
     * @param bool $bool
     * @return $this
     */
    public function enableIdentityMap(bool $bool = true): static
    {
        $this->enableIdentityMap = $bool;

        return $this;
    }
}
