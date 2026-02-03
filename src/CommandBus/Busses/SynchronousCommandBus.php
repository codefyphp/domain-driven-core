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
use Codefy\CommandBus\Exceptions\UnresolvableCommandHandlerException;
use Codefy\CommandBus\Resolvers\NativeCommandHandlerResolver;
use ReflectionException;

class SynchronousCommandBus implements CommandBus
{
    public function __construct(protected CommandHandlerResolver $resolver = new NativeCommandHandlerResolver())
    {
    }

    /**
     * Execute a command.
     *
     * @throws UnresolvableCommandHandlerException
     * @throws ReflectionException
     */
    public function execute(Command $command): mixed
    {
        $handler = $this->resolver->resolve($command);

        return $handler->handle(command: $command);
    }
}
