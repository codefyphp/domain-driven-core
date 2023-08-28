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

namespace Codefy\CommandBus\Decorators;

interface EventDispatcher
{
    /**
     * @param string $event The event name
     * @param array $data The event data
     * @return void
     */
    public function dispatch(string $event, array $data = []): void;
}
