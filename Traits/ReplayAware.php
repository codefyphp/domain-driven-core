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

use Codefy\Domain\EventSourcing\DomainEvent;
use Codefy\Domain\EventSourcing\EventStream;

trait ReplayAware
{
    protected function replay(EventStream $history): void
    {
        foreach ($history as $event) {
            /** @var DomainEvent $event */
            $this->playhead = $event->playhead() ?? $this->playhead() + 1;
            $this->applyThat($event);
        }
    }
}
