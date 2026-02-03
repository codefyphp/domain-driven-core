<?php

/**
 * CodefyPHP
 *
 * @link       https://github.com/codefyphp/domain-driven-core
 * @copyright  2022 Joshua Parker <joshua@joshuaparker.dev>
 * @copyright  2015 Adam Nicholson <adamnicholson10@gmail.com>
 * @license    https://opensource.org/licenses/mit-license.php MIT License
 */

declare(strict_types=1);

namespace Codefy\CommandBus\Decorators;

use Codefy\CommandBus\Busses\SynchronousCommandBus;
use Codefy\CommandBus\Command;
use Codefy\CommandBus\CommandBus;
use Codefy\CommandBus\CommandQueuer;
use Codefy\CommandBus\Decorator;
use Codefy\CommandBus\QueueableCommand;
use Codefy\CommandBus\Traits\InnerBusAware;

/**
 * Queue commands which implement QueueableCommand into a CommandQueuer.
 */
class CommandQueueingDecorator implements Decorator
{
    use InnerBusAware;

    protected CommandQueuer $queuer;

    public function __construct(CommandQueuer $queuer, ?CommandBus $innerCommandBus = null)
    {
        $this->queuer = $queuer;
        $this->setInnerBus(bus: $innerCommandBus ?: new SynchronousCommandBus());
    }

    /**
     * Execute a command.
     */
    public function execute(Command $command): mixed
    {
        if ($command instanceof QueueableCommand) {
            $this->queuer->queue(command: $command);
            return null;
        }

        return $this->innerCommandBus->execute(command: $command);
    }
}
