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
