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

namespace Codefy\QueryBus\Handlers;

use Codefy\CommandBus\Container;
use Codefy\QueryBus\Query;
use Codefy\QueryBus\QueryHandler;

class LazyLoadingQueryHandler implements QueryHandler
{
    /**
     * @param string $handlerName
     * @param Container $container
     */
    public function __construct(
        public readonly string $handlerName,
        public readonly Container $container
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
