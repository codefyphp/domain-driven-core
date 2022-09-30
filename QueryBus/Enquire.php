<?php

/**
 * CodefyPHP
 *
 * @link       https://github.com/codefyphp/codefy
 * @copyright  2022
 * @author     Joshua Parker <josh@joshuaparker.blog>
 * @license    https://opensource.org/licenses/mit-license.php MIT License
 *
 * @since      0.2.0
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
