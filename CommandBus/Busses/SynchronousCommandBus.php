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

namespace Codefy\CommandBus\Busses;

use Codefy\CommandBus\Command;
use Codefy\CommandBus\CommandBus;
use Codefy\CommandBus\CommandHandlerResolver;
use Codefy\CommandBus\Exceptions\CommandCouldNotBeHandledException;
use Codefy\CommandBus\Exceptions\UnresolvableCommandHandlerException;
use Codefy\CommandBus\Resolvers\NativeCommandHandlerResolver;
use ReflectionException;

use function method_exists;
use function sprintf;

class SynchronousCommandBus implements CommandBus
{
    protected ?CommandHandlerResolver $resolver;

    public function __construct(?CommandHandlerResolver $resolver = null)
    {
        $this->resolver = $resolver ?: new NativeCommandHandlerResolver();
    }

    /**
     * Execute a command.
     *
     * @throws UnresolvableCommandHandlerException|ReflectionException|CommandCouldNotBeHandledException
     */
    public function execute(Command $command): mixed
    {
        $handler = $this->resolver->resolve($command);

        if (!method_exists(object_or_class: $handler, method: 'handle')) {
            throw new CommandCouldNotBeHandledException(
                sprintf(
                    'The command %s could not be handled by %s',
                    get_class(object: $command),
                    get_class(object: $handler)
                )
            );
        }

        return $handler->handle(command: $command);
    }
}
