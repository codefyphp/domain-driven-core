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

use Codefy\Domain\Aggregate\RecordsEvents;

/**
 * A unit of work that acts both as an identity map and a change tracker.
 * It does not commit/save/persist. Its role is reduced to tracking multiple
 * aggregates, and to hand you back those that have changed. Persisting the
 * ones that have changed or happened on the outside.
 */
interface UnitOfWork extends IdentityMap
{
    /**
     * Returns AggregateRoots that have changed.
     *
     * @return RecordsEvents[]
     */
    public function getChanges(): array;
}
