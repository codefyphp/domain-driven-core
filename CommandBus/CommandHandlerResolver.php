<?php

/**
 * CodefyPHP
 *
 * @link       https://github.com/codefyphp/codefy
 * @copyright  2022 Joshua Parker <josh@joshuaparker.blog>
 * @copyright  2015 Adam Nicholson <adamnicholson10@gmail.com>
 * @license    https://opensource.org/licenses/mit-license.php MIT License
 *
 * @since      1.0.0
 */

declare(strict_types=1);

namespace Codefy\CommandBus;

interface CommandHandlerResolver
{
    /**
     * Retrieve a CommandHandler for a given Command.
     */
    public function resolve(Command $command): CommandHandler;

    /**
     * Bind a handler to a command. These bindings should overrule the default
     * resolution behavior for this resolver.
     */
    public function bindHandler(string $commandName, CommandHandler|callable|string $handler);
}
