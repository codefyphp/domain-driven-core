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

namespace Codefy\QueryBus\Busses;

use Codefy\QueryBus\Query;
use Codefy\QueryBus\QueryBus;
use Codefy\QueryBus\QueryHandlerResolver;
use Codefy\QueryBus\Resolvers\NativeQueryHandlerResolver;
use Codefy\QueryBus\UnresolvableQueryHandlerException;
use ReflectionException;

class SynchronousQueryBus implements QueryBus
{
    protected ?QueryHandlerResolver $resolver;

    public function __construct(?QueryHandlerResolver $resolver = null)
    {
        $this->resolver = $resolver ?: new NativeQueryHandlerResolver();
    }

    /**
     * Execute a command.
     *
     * @throws UnresolvableQueryHandlerException|ReflectionException
     */
    public function execute(Query $query): mixed
    {
        $handler = $this->resolver->resolve(query: $query);

        return $handler->handle(query: $query);
    }
}
