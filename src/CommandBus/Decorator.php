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

interface Decorator extends CommandBus
{
    /**
     * Set the CommandBus which we're decorating.
     *
     * @param CommandBus $bus
     * @return void
     */
    public function setInnerBus(CommandBus $bus): void;
}
