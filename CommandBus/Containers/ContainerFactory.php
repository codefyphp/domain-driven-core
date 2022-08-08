<?php

/**
 * CodefyPHP
 *
 * @link       https://github.com/codefyphp/codefy
 * @copyright  2022 Joshua Parker <josh@joshuaparker.blog>
 * @license    https://opensource.org/licenses/mit-license.php MIT License
 *
 * @since      1.0.0
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
