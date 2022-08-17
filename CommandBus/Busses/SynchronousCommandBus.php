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

namespace Codefy\CommandBus\Busses;

use Codefy\CommandBus\Command;
use Codefy\CommandBus\CommandBus;
use Codefy\CommandBus\CommandHandlerResolver;
use Codefy\CommandBus\Exceptions\UnresolvableCommandHandlerException;
use Codefy\CommandBus\Resolvers\NativeCommandHandlerResolver;
use ReflectionException;

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
     * @throws UnresolvableCommandHandlerException|ReflectionException
     */
    public function execute(Command $command): mixed
    {
        $handler = $this->resolver->resolve($command);

        return $handler->handle($command);
    }
}
