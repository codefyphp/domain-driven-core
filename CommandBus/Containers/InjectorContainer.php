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
use Qubus\Exception\Http\Client\NotFoundException;
use Qubus\Injector\Psr11\Container as InjectorBridgeContainer;
use Qubus\Injector\Psr11\ContainerException;

readonly class InjectorContainer implements Container
{
    public function __construct(public InjectorBridgeContainer $container)
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
