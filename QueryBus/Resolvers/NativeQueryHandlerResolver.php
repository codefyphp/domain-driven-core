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

namespace Codefy\QueryBus\Resolvers;

use Codefy\CommandBus\Container;
use Codefy\CommandBus\Containers\NativeContainer;
use Codefy\QueryBus\Handlers\CallableQueryHandler;
use Codefy\QueryBus\Handlers\LazyLoadingQueryHandler;
use Codefy\QueryBus\Query;
use Codefy\QueryBus\QueryHandler;
use Codefy\QueryBus\QueryHandlerResolver;
use Codefy\QueryBus\UnresolvableQueryHandlerException;
use Qubus\Exception\Data\TypeException;
use ReflectionException;

use function array_pop;
use function class_exists;
use function explode;
use function implode;
use function is_callable;
use function is_string;
use function sprintf;

class NativeQueryHandlerResolver implements QueryHandlerResolver
{
    protected Container $container;

    protected array $handlers = [];

    public function __construct(?Container $container = null)
    {
        $this->container = $container ?: new NativeContainer();
    }

    /**
     * Retrieve a QueryHandler for a given Command
     *
     * @throws UnresolvableQueryHandlerException|ReflectionException
     */
    public function resolve(Query $query): QueryHandler
    {
        $queryName = $query::class;

        // Find the QueryHandler if it has been manually defined using pushHandler()
        foreach ($this->handlers as $handlerQuery => $handler) {
            if ($handlerQuery === $queryName) {
                return $handler;
            }
        }

        // If the Command also implements QueryHandler, then it can handle() itself
        if ($query instanceof QueryHandler) {
            return $query;
        }

        // Try and guess the handler's name in the same namespace with suffix "Handler"
        $class = $queryName . 'Handler';
        if (class_exists(class: $class)) {
            return $this->container->make($class);
        }

        // Try and guess the handler's name in nested "Handlers" namespace with suffix "Handler"
        $classParts = explode(separator: '\\', string: $queryName);
        $queryNameWithoutNamespace = array_pop($classParts);
        $class = implode(separator: '\\', array: $classParts) . '\\Handlers\\' . $queryNameWithoutNamespace . 'Handler';
        if (class_exists(class: $class)) {
            return $this->container->make($class);
        }

        throw new UnresolvableQueryHandlerException(
            sprintf(
                'Could not resolve a handler for [%s].',
                $query::class
            )
        );
    }

    /**
     * Bind a handler to a query. These bindings should overrule the default
     * resolution behavior for this resolver.
     *
     * @param string $queryName
     * @param callable|string|QueryHandler $handler
     * @return void
     * @throws TypeException
     */
    public function bindHandler(string $queryName, callable|string|QueryHandler $handler): void
    {
        // If the $handler given is an instance of QueryHandler, simply bind it.
        if ($handler instanceof QueryHandler) {
            $this->handlers[$queryName] = $handler;
            return;
        }

        // If the handler given is callable, wrap it up in a CallableQueryHandler for executing later.
        if (is_callable(value: $handler)) {
            $this->bindHandler($queryName, new CallableQueryHandler($handler));
        }

        // If the handler given is a string, wrap it up in a LazyLoadingQueryHandler for loading later.
        if (is_string(value: $handler)) {
            $this->bindHandler($queryName, new LazyLoadingQueryHandler($handler, $this->container));
        }

        throw new TypeException(
            'Could not push handler. Query Handlers should be an
            instance of Codefy\QueryBus\QueryHandler, a callable, 
            or a string representing a QueryHandler class.'
        );
    }
}
