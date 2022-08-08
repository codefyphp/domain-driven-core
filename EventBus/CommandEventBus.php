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

namespace Codefy\EventBus;

use BadMethodCallException;
use Codefy\Domain\EventSourcing\DomainEvent;
use Codefy\Domain\EventSourcing\EventStore;

use function spl_object_hash;

final class CommandEventBus implements EventBus
{
    private array $subscribers;

    public function __construct(
        public readonly EventStore $eventStore,
        public readonly DomainEventPublisher $publisher
    ) {
        $this->subscribers = [];
    }

    public function __clone()
    {
        throw new BadMethodCallException(message: 'Clone is not supported.');
    }

    public function subscribe(DomainEventSubscriber $subscriber): void
    {
        $key = $this->getSubscriberUniqueKey($subscriber);
        $this->subscribers[$key] = $subscriber;
    }

    public function unsubscribe(DomainEventSubscriber $subscriber): void
    {
        $key = $this->getSubscriberUniqueKey($subscriber);
        if (isset($this->subscribers[$key])) {
            unset($this->subscribers[$key]);
        }
    }

    public function publish(DomainEvent ...$events): void
    {
        foreach ($events as $event) {
            $this->eventStore->append(event: $event);
            foreach ($this->subscribers as $subscriber) {
                if ($subscriber->isSubscribedTo($event)) {
                    $subscriber->handle($event);
                }
            }
            $this->publisher->publish(event: $event);
        }
    }

    private function getSubscriberUniqueKey(DomainEventSubscriber $subscriber): string
    {
        return spl_object_hash($subscriber);
    }
}
