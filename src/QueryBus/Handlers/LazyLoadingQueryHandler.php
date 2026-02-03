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

namespace Codefy\QueryBus\Handlers;

use Codefy\CommandBus\Container;
use Codefy\QueryBus\Query;
use Codefy\QueryBus\QueryHandler;

readonly class LazyLoadingQueryHandler implements QueryHandler
{
    /**
     * @param string $handlerName
     * @param Container $container
     */
    public function __construct(
        public string $handlerName,
        public Container $container
    ) {
    }

    /**
     * Handle a query execution.
     *
     * @param Query $query
     * @return mixed
     */
    public function handle(Query $query): mixed
    {
        $handler = $this->container->make($this->handlerName);

        return $handler->handle($query);
    }
}
