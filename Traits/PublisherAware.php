<?php

/**
 * CodefyPHP
 *
 * @link       https://github.com/codefyphp/codefy
 * @copyright  2022
 * @author     Joshua Parker <josh@joshuaparker.blog>
 * @license    https://opensource.org/licenses/mit-license.php MIT License
 *
 * @since      0.1.0
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
