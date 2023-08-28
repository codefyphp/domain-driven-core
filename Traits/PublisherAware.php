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
use Codefy\EventBus\GenericPublisher;

trait PublisherAware
{
    protected function publishThat(DomainEvent $event): void
    {
        GenericPublisher::instance()->publish($event);
    }
}
