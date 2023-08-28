<?php

/**
 * CodefyPHP
 *
 * @link       https://github.com/codefyphp/domain-driven-core
 * @copyright  2022
 * @author     Joshua Parker <joshua@joshuaparker.dev>
 * @license    https://opensource.org/licenses/mit-license.php MIT License
 */

declare(strict_types=1);

namespace Codefy\CommandBus;

use Codefy\CommandBus\Traits\PayloadAware;
use stdClass;

class PayloadCommand extends stdClass implements Command
{
    use PayloadAware;
}
