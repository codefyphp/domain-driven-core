<?php

/**
 * CodefyPHP
 *
 * @link       https://github.com/codefyphp/codefy
 * @copyright  2022 Joshua Parker <josh@joshuaparker.blog>
 * @copyright  2015 Adam Nicholson <adamnicholson10@gmail.com>
 * @license    https://opensource.org/licenses/mit-license.php MIT License
 *
 * @since      0.1.0
 */

declare(strict_types=1);

namespace Codefy\CommandBus\Decorators;

use Codefy\CommandBus\Busses\SynchronousCommandBus;
use Codefy\CommandBus\Command;
use Codefy\CommandBus\CommandBus;
use Codefy\CommandBus\Decorator;
use Qubus\Exception\Exception;

use function str_replace;

class EventDispatchingDecorator implements Decorator
{
    protected EventDispatcher $dispatcher;

    protected ?CommandBus $innerCommandBus = null;

    public function __construct(EventDispatcher $dispatcher, ?CommandBus $innerCommandBus = null)
    {
        $this->dispatcher = $dispatcher;
        $this->setInnerBus(bus: $innerCommandBus ?: new SynchronousCommandBus());
    }

    public function setInnerBus(CommandBus $bus): void
    {
        $this->innerCommandBus = $bus;
    }

    /**
     * Execute a command and dispatch and event.
     *
     * @throws Exception
     */
    public function execute(Command $command): mixed
    {
        if (!$this->innerCommandBus) {
            throw new Exception(
                message: 'No inner bus defined for this decorator. Set an inner bus with `setInnerBus()`.'
            );
        }

        $response = $this->innerCommandBus->execute(command: $command);

        $eventName = $this->getEventName(command: $command);

        $this->dispatcher->dispatch(event: $eventName, data: [$command]);

        return $response;
    }

    /**
     * Get the event name for a given Command.
     */
    protected function getEventName(Command $command): string
    {
        return str_replace(search: '\\', replace: '.', subject: $command::class);
    }
}
