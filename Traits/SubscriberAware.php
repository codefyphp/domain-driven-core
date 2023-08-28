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

trait SubscriberAware
{
    /** {@inheritDoc} */
    public function isSubscribedTo(DomainEvent $event): bool
    {
        return in_array(get_class($event), $this->eventType);
    }
}
