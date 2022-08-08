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

namespace Codefy\CommandBus\Containers;

use Codefy\CommandBus\Container;
use ReflectionClass;
use ReflectionException;

class NativeContainer implements Container
{
    /**
     * Instantiate and return an object based on its class name.
     *
     * @throws ReflectionException
     */
    public function make(string $className): ?object
    {
        // Use reflection to get the list of constructor dependencies
        $class = new ReflectionClass($className);
        $constructor = $class->getConstructor();

        // if no constructor, pop smoke and move out!
        if (! $constructor) {
            return $class->newInstance();
        }

        $parameters = $constructor->getParameters();

        // Fetch each of the dependencies from the factory, and make validators
        // via their fully namespaced name.
        $dependencies = [];
        foreach ($parameters as $parameter) {
            $dependencies[] = $this->make(
                $parameter->getType()->getName()
            );
        }

        // Init the class with our list of introspected dependencies
        return $class->newInstanceArgs($dependencies);
    }
}
