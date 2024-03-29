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

namespace Codefy\CommandBus\Containers;

use Codefy\CommandBus\Container;
use Qubus\Injector\Config\InjectorConfig;
use Qubus\Injector\Psr11\Container as Psr11Container;

class ContainerFactory
{
    public static function make(array $config): Container
    {
        return new InjectorContainer(container: new Psr11Container(config: new InjectorConfig(config: $config)));
    }
}
