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

namespace Codefy\CommandBus\Resolvers;

use Codefy\CommandBus\Command;
use Codefy\CommandBus\CommandHandler;
use Codefy\CommandBus\CommandHandlerResolver;
use Codefy\CommandBus\Container;
use Codefy\CommandBus\Containers\NativeContainer;
use Codefy\CommandBus\Exceptions\UnresolvableCommandHandlerException;
use Codefy\CommandBus\Handlers\CallableCommandHandler;
use Codefy\CommandBus\Handlers\LazyLoadingCommandHandler;
use Qubus\Exception\Data\TypeException;
use ReflectionException;

use function array_pop;
use function class_exists;
use function explode;
use function implode;
use function is_callable;
use function is_string;
use function sprintf;

class NativeCommandHandlerResolver implements CommandHandlerResolver
{
    protected Container $container;

    protected array $handlers = [];

    public function __construct(?Container $container = null)
    {
        $this->container = $container ?: new NativeContainer();
    }

    /**
     * Retrieve a CommandHandler for a given Command
     *
     * @throws UnresolvableCommandHandlerException
     * @throws ReflectionException
     */
    public function resolve(Command $command): CommandHandler
    {
        $commandName = $command::class;

        // Find the CommandHandler if it has been manually defined using pushHandler()
        foreach ($this->handlers as $handlerCommand => $handler) {
            if ($handlerCommand === $commandName) {
                return $handler;
            }
        }

        // If the Command also implements CommandHandler, then it can handle() itself
        if ($command instanceof CommandHandler) {
            return $command;
        }

        // Try and guess the handler's name in the same namespace with suffix "Handler"
        $class = sprintf("$commandName%s", 'Handler');
        if (class_exists(class: $class)) {
            return $this->container->make($class);
        }

        // Try and guess the handler's name in nested "Handlers" namespace with suffix "Handler"
        $classParts = explode(separator: '\\', string: $commandName);
        $commandNameWithoutNamespace = array_pop($classParts);
        $class = implode(
            separator: '\\',
            array: $classParts
        ) . '\\Handlers\\' . $commandNameWithoutNamespace . 'Handler';
        if (class_exists(class: $class)) {
            return $this->container->make($class);
        }

        throw new UnresolvableCommandHandlerException(
            sprintf(
                'Could not resolve a handler for [%s].',
                $command::class
            )
        );
    }

    /**
     * Bind a handler to a command. These bindings should overrule the default
     * resolution behavior for this resolver
     *
     * @throws TypeException
     */
    public function bindHandler(string $commandName, callable|CommandHandler|string $handler): mixed
    {
        // If the $handler given is an instance of CommandHandler, simply bind it.
        if ($handler instanceof CommandHandler) {
            $this->handlers[$commandName] = $handler;
            return null;
        }

        // If the handler given is callable, wrap it up in a CallableCommandHandler for executing later.
        if (is_callable(value: $handler)) {
            return $this->bindHandler(commandName: $commandName, handler: new CallableCommandHandler($handler));
        }

        // If the handler given is a string, wrap it up in a LazyLoadingCommandHandler for loading later.
        if (is_string(value: $handler)) {
            return $this->bindHandler(
                commandName: $commandName,
                handler: new LazyLoadingCommandHandler(handlerName: $handler, container: $this->container)
            );
        }

        throw new TypeException(
            message: 'Could not push handler. Command Handlers should be an
            instance of Codefy\CommandBus\CommandHandler, a callable, 
            or a string representing a CommandHandler class.'
        );
    }
}
