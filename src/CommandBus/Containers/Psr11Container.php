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
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Qubus\Exception\Http\Client\NotFoundException;
use Qubus\Injector\Psr11\ContainerException;

readonly class Psr11Container implements Container
{
    public function __construct(public ContainerInterface $container)
    {
    }

    /**
     * {@inheritDoc}
     */
    public function make(string $className): mixed
    {
        try {
            return $this->container->get($className);
        } catch (NotFoundException | NotFoundExceptionInterface | ContainerException | ContainerExceptionInterface $e) {
            return $e;
        }
    }
}
