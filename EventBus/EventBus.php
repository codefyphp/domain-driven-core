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

namespace Codefy\EventBus;

use Codefy\Domain\EventSourcing\DomainEvent;

interface EventBus
{
    public function publish(DomainEvent ...$events): void;

    public function subscribe(DomainEventSubscriber $subscriber): void;

    public function unsubscribe(DomainEventSubscriber $subscriber): void;
}
