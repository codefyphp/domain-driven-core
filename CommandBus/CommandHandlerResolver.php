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
