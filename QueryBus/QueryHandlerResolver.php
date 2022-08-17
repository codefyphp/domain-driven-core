<?php

/**
 * CodefyPHP
 *
 * @link       https://github.com/codefyphp/codefy
 * @copyright  2022 Joshua Parker <josh@joshuaparker.blog>
 * @copyright  2015 Adam Nicholson <adamnicholson10@gmail.com>
 * @license    https://opensource.org/licenses/mit-license.php MIT License
 *
 * @since      0.1.0
 */

declare(strict_types=1);

namespace Codefy\QueryBus;

use Codefy\QueryBus\Query;
use Codefy\QueryBus\QueryHandler;

interface QueryHandlerResolver
{
    /**
     * Retrieve a $queryHandler for a given Query.
     */
    public function resolve(Query $query): QueryHandler;

    /**
     * Bind a handler to a query. These bindings should overrule the default
     * resolution behavior for this resolver.
     */
    public function bindHandler(string $queryName, QueryHandler|callable|string $handler): void;
}
