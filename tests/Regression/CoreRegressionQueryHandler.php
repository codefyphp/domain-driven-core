<?php

declare(strict_types=1);

namespace Codefy\Tests\Regression;

use Codefy\QueryBus\Query;
use Codefy\QueryBus\QueryHandler;

final class CoreRegressionQueryHandler implements QueryHandler
{
    public function handle(Query $query): mixed
    {
        return $query->value;
    }
}
