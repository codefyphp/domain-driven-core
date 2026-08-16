<?php

declare(strict_types=1);

namespace Codefy\Tests\Regression;

final readonly class CoreRegressionConsumer
{
    public function __construct(public CoreRegressionDependency $dependency)
    {
    }
}
