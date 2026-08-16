<?php

declare(strict_types=1);

namespace Codefy\Tests\Regression;

use Codefy\CommandBus\Command;

final readonly class CoreRegressionCommand implements Command
{
    public function __construct(public string $name)
    {
    }
}
