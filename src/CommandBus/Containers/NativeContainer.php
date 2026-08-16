<?php

/**
 * CodefyPHP
 *
 * @link       https://github.com/codefyphp/domain-driven-core
 * @copyright  2022 Joshua Parker <joshua@joshuaparker.dev>
 * @copyright  2015 Adam Nicholson <adamnicholson10@gmail.com>
 * @license    https://opensource.org/licenses/mit-license.php MIT License
 */

declare(strict_types=1);

namespace Codefy\CommandBus\Containers;

use Codefy\CommandBus\Container;

class NativeContainer implements Container
{
    /**
     * {@inheritDoc}
     * @throws \ReflectionException
     */
    public function make(string $className): ?object
    {
        // Use reflection to get the list of constructor dependencies
        $class = new \ReflectionClass($className);
        $constructor = $class->getConstructor();

        // if no constructor, pop smoke and move out!
        if (!$constructor) {
            return $class->newInstance();
        }

        $parameters = $constructor->getParameters();

        // Resolve object dependencies by their declared type. Constructor
        // parameter names are not class names and cannot be autowired safely.
        $dependencies = [];
        foreach ($parameters as $parameter) {
            $type = $parameter->getType();

            if ($type instanceof \ReflectionNamedType && !$type->isBuiltin()) {
                $dependencies[] = $this->make($type->getName());
                continue;
            }

            if ($parameter->isDefaultValueAvailable()) {
                $dependencies[] = $parameter->getDefaultValue();
                continue;
            }

            if ($parameter->allowsNull()) {
                $dependencies[] = null;
                continue;
            }

            throw new \ReflectionException(
                sprintf(
                    'Cannot autowire parameter $%s of %s::__construct().',
                    $parameter->getName(),
                    $className
                )
            );
        }

        // Init the class with our list of introspected dependencies
        return $class->newInstanceArgs($dependencies);
    }
}
