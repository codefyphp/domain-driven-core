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
    protected ?QueryBus $bus = null;

    /**
     * Constructor.
     *
     * @param QueryBus|null $bus
     */
    public function __construct(?QueryBus $bus = null)
    {
        $this->bus = $bus ?: new SynchronousQueryBus();
    }

    /**
     * Execute a query.
     *
     * @throws ReflectionException
     * @throws UnresolvableQueryHandlerException
     */
    public function execute(Query $query): mixed
    {
        return $this->bus->execute(query: $query);
    }
}
