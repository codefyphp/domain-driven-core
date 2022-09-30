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

namespace Codefy\Tests;

use Codefy\EventBus\DomainEventSubscriber;
use Codefy\Traits\SubscriberAware;

final class TitleWasChangedSubscriber implements DomainEventSubscriber
{
    use SubscriberAware;

    protected array $eventType = [
        TitleWasChanged::class,
    ];

    public function __construct(public readonly PostProjection $projection)
    {
    }

    public function handle(TitleWasChanged $event): void
    {
        $this->projection->projectWhenTitleWasChanged(event: $event);
    }
}
