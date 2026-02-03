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

namespace Codefy\CommandBus;

use Codefy\CommandBus\Busses\SynchronousCommandBus;
use Codefy\CommandBus\Exceptions\UnresolvableCommandHandlerException;
use ReflectionException;

/**
 * The main Odin class is a CommandBus, which is effectively a decorator
 * around another CommandBus interface.
 */
class Odin implements CommandBus
{
    /**
     * Constructor
     *
     * @param array<Decorator> $decorators Array of \Codefy\CommandBus\Decorator objects
     */
    public function __construct(protected CommandBus $bus = new SynchronousCommandBus(), array $decorators = [])
    {
        foreach ($decorators as $decorator) {
            $this->pushDecorator(decorator: $decorator);
        }
    }

    /**
     * Push a new Decorator on to the stack.
     */
    public function pushDecorator(Decorator $decorator): void
    {
        $decorator->setInnerBus(bus: $this->bus);
        $this->bus = $decorator;
    }

    /**
     * Execute a command.
     *
     * @throws UnresolvableCommandHandlerException
     * @throws ReflectionException
     */
    public function execute(Command $command): mixed
    {
        return $this->bus->execute(command: $command);
    }
}
