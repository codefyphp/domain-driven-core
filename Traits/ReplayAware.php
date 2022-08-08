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

namespace Codefy\Traits;

use Codefy\Domain\Aggregate\EventStream;
use Codefy\Domain\EventSourcing\DomainEvent;

trait ReplayAware
{
    protected function replay(EventStream $history): void
    {
        foreach ($history as $event) {
            /** @var DomainEvent $event */
            $this->aggregateVersion = $event->aggregateVersion() ?? $this->aggregateRootVersion() + 1;
            $this->applyThat($event);
        }
    }
}
