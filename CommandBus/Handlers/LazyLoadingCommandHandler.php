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

namespace Codefy\CommandBus\Handlers;

use Codefy\CommandBus\Command;
use Codefy\CommandBus\CommandHandler;
use Codefy\CommandBus\Container;
use Codefy\CommandBus\Exceptions\CommandCouldNotBeHandledException;

use function get_class;
use function method_exists;
use function sprintf;

readonly class LazyLoadingCommandHandler implements CommandHandler
{
    /**
     * @param string $handlerName
     * @param Container $container
     */
    public function __construct(public string $handlerName, public Container $container)
    {
    }

    /**
     * Handle a command execution.
     *
     * @param Command $command
     * @return mixed|void
     * @throws CommandCouldNotBeHandledException
     */
    public function handle(Command $command)
    {
        $handler = $this->container->make($this->handlerName);

        if (!method_exists(object_or_class: $handler, method: 'handle')) {
            throw new CommandCouldNotBeHandledException(
                sprintf(
                    'The command %s could not be handled by %s',
                    get_class(object: $command),
                    $this->handlerName
                )
            );
        }

        return $handler->handle($command);
    }
}
