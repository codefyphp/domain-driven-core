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

namespace Codefy\CommandBus\Traits;

use Codefy\CommandBus\CommandBus;

trait InnerBusAware
{
    protected CommandBus $innerCommandBus;

    public function setInnerBus(CommandBus $bus): void
    {
        $this->innerCommandBus = $bus;
    }
}
