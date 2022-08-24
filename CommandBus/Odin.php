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

namespace Codefy\CommandBus;

use Codefy\CommandBus\Busses\SynchronousCommandBus;
use Codefy\CommandBus\Exceptions\CommandCouldNotBeHandledException;
use Codefy\CommandBus\Exceptions\UnresolvableCommandHandlerException;
use ReflectionException;

/**
 * The main Odin class is a CommandBus, which is effectively a decorator
 * around another CommandBus interface.
 */
class Odin implements CommandBus
{
    protected ?CommandBus $bus = null;

    /**
     * Constructor
     *
     * @param array $decorators Array of \Codefy\CommandBus\Decorator objects
     */
    public function __construct(?CommandBus $bus = null, array $decorators = [])
    {
        $this->bus = $bus ?: new SynchronousCommandBus();

        foreach ($decorators as $decorator) {
            $this->pushDecorator(decorator: $decorator);
        }
    }

    /**
     * Push a new Decorator on to the stack.
     */
    public function pushDecorator(Decorator $decorator)
    {
        $decorator->setInnerBus(bus: $this->bus);
        $this->bus = $decorator;
    }

    /**
     * Execute a command.
     *
     * @throws UnresolvableCommandHandlerException|ReflectionException|CommandCouldNotBeHandledException
     */
    public function execute(Command $command): mixed
    {
        return $this->bus->execute(command: $command);
    }
}
