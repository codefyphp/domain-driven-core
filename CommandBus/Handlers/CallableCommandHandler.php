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

namespace Codefy\CommandBus\Handlers;

use Codefy\CommandBus\Command;
use Codefy\CommandBus\CommandHandler;

class CallableCommandHandler implements CommandHandler
{
    /** @var callable */
    protected $handler;

    public function __construct(callable $handler)
    {
        $this->handler = $handler;
    }

    /**
     * Handle a command execution
     *
     * @return mixed|void
     */
    public function handle(Command $command)
    {
        $callableHandler = $this->handler;
        return $callableHandler($command);
    }
}
