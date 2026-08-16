<?php

declare(strict_types=1);

namespace Codefy\Tests\Regression;

use Codefy\QueryBus\Query;

final readonly class CoreRegressionQuery implements Query
{
    public function __construct(public string $value)
    {
    }
}
