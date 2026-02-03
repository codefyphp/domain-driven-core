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

namespace Codefy\QueryBus;

use Codefy\QueryBus\Busses\SynchronousQueryBus;
use ReflectionException;

class Enquire implements QueryBus
{
    /**
     * Constructor.
     *
     * @param QueryBus $bus
     */
    public function __construct(protected QueryBus $bus = new SynchronousQueryBus())
    {
    }

    /**
     * Execute a query.
     *
     * @throws UnresolvableQueryHandlerException
     * @throws ReflectionException
     */
    public function execute(Query $query): mixed
    {
        return $this->bus->execute(query: $query);
    }
}
