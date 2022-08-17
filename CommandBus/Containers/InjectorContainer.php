<?php

/**
 * CodefyPHP
 *
 * @link       https://github.com/codefyphp/codefy
 * @copyright  2022
 * @author     Joshua Parker <josh@joshuaparker.blog>
 * @license    https://opensource.org/licenses/mit-license.php MIT License
 *
 * @since      0.1.0
 */

declare(strict_types=1);

namespace Codefy\CommandBus\Containers;

use Codefy\CommandBus\Container;
use Qubus\Exception\Http\Client\NotFoundException;
use Qubus\Injector\Psr11\Container as InjectorBridgeContainer;
use Qubus\Injector\Psr11\ContainerException;

class InjectorContainer implements Container
{
    public function __construct(public readonly InjectorBridgeContainer $container)
    {
    }

    /**
     * {@inheritDoc}
     */
    public function make($class): mixed
    {
        try {
            return $this->container->get($class);
        } catch (NotFoundException | ContainerException $e) {
            return $e;
        }
    }
}
