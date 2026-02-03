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

use Codefy\QueryBus\Query;
use Codefy\QueryBus\QueryHandler;

class CallableQueryHandler implements QueryHandler
{
    /** @var callable */
    protected $handler;

    public function __construct(callable $handler)
    {
        $this->handler = $handler;
    }

    /**
     * Handle a query execution.
     *
     * @param Query $query
     * @return mixed
     */
    public function handle(Query $query): mixed
    {
        $callableHandler = $this->handler;
        return $callableHandler($query);
    }
}
