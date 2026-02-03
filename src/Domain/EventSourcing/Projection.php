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

interface Projection
{
    /**
     * Project a set of domain events.
     *
     * @param DomainEvent ...$events
     * @return void
     */
    public function project(DomainEvent ...$events): void;
}
