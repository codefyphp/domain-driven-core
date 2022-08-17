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
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class Psr11Container implements Container
{
    public function __construct(public readonly ContainerInterface $container)
    {
    }

    /**
     * {@inheritDoc}
     */
    public function make($class): mixed
    {
        try {
            return $this->container->get($class);
        } catch (NotFoundExceptionInterface|ContainerExceptionInterface $e) {
            return $e;
        }
    }
}
