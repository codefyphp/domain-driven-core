<?php

declare(strict_types=1);

namespace Codefy\Tests\Regression;

use Codefy\CommandBus\Command;
use Codefy\CommandBus\CommandBus;

final class CoreRegressionReentrantBus implements CommandBus
{
    public ?CommandBus $outerBus = null;

    /** @var list<string> */
    public array $calls = [];

    public function execute(Command $command): mixed
    {
        $this->calls[] = $command->name;

        if ($command->name === 'outer') {
            $this->outerBus?->execute(new CoreRegressionCommand('nested'));
        }

        return $command->name;
    }
}
