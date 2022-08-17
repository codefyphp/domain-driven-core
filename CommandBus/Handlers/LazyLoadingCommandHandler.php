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

namespace Codefy\CommandBus\Handlers;

use Codefy\CommandBus\Command;
use Codefy\CommandBus\CommandHandler;
use Codefy\CommandBus\Container;

class LazyLoadingCommandHandler implements CommandHandler
{
    /**
     * @param string $handlerName
     * @param Container $container
     */
    public function __construct(public readonly string $handlerName, public readonly Container $container)
    {
    }

    /**
     * Handle a command execution.
     *
     * @param Command $command
     * @return mixed|void
     */
    public function handle(Command $command)
    {
        $handler = $this->container->make($this->handlerName);

        return $handler->handle($command);
    }
}
