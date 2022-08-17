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

final class GenericPublisher implements DomainEventPublisher
{
    /** @var array $subscribers */
    protected array $subscribers = [];

    /** @var DomainEventPublisher|null $instance */
    protected static ?DomainEventPublisher $instance = null;

    public static function instance(): DomainEventPublisher
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private function __construct()
    {
        $this->subscribers = [];
    }

    public function subscribe(DomainEventSubscriber $subscriber): void
    {
        $this->subscribers[$subscriber::class] = $subscriber;
    }

    public function publish(DomainEvent $event): void
    {
        foreach ($this->subscribers as $subscriber) {
            if ($subscriber->isSubscribedTo($event)) {
                $subscriber->handle($event);
            }
        }
    }
}
