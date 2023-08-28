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
use Codefy\CommandBus\Decorator;
use Codefy\CommandBus\Traits\InnerBusAware;

/**
 * TransactionalCommandLockingDecorator treats commands as transactions. Meaning that any
 * subsequent Commands passed to the bus from inside the relevant CommandHandler
 * will not be executed until the initial command is completed.
 */
class TransactionalCommandLockingDecorator implements Decorator
{
    use InnerBusAware;

    /**
     * Whether a Command is in progress and the bus is locked.
     */
    protected bool $locked = false;

    /**
     * Queued Commands to be executed when the current command finishes.
     *
     * @var array
     */
    protected array $queue = [];

    public function __construct(?CommandBus $innerCommandBus = null)
    {
        $this->setInnerBus(bus: $innerCommandBus ?: new SynchronousCommandBus());
    }

    /**
     * Execute a command
     *
     * @param Command $command
     * @return mixed
     */
    public function execute(Command $command): mixed
    {
        if ($this->locked === true) {
            $this->queue[] = $command;
            return null;
        }

        $this->locked = true;

        $response = $this->executeIgnoringLock(command: $command);

        $this->executeQueue();

        $this->locked = false;

        return $response;
    }

    /**
     * Execute a command, regardless of the lock
     *
     * @param Command $command
     * @return mixed
     */
    protected function executeIgnoringLock(Command $command): mixed
    {
        return $this->innerCommandBus->execute(command: $command);
    }

    /**
     * Execute all queued commands
     */
    protected function executeQueue(): void
    {
        foreach ($this->queue as $command) {
            $this->executeIgnoringLock(command: $command);
        }
    }
}
