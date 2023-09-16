<?php

declare(strict_types=1);

namespace Codefy\Tests\CommandBus;

use Codefy\Domain\Aggregate\AggregateNotFoundException;
use Codefy\Domain\Aggregate\AggregateRepository;
use Codefy\Domain\Aggregate\MultipleInstancesOfAggregateDetectedException;
use Codefy\Tests\Domain\TitleWasChanged;
use Qubus\Exception\Data\TypeException;

readonly class ChangeTitleCommandHandler
{
    public function __construct(public AggregateRepository $aggregateRepository)
    {
    }

    /**
     * @throws TypeException
     * @throws AggregateNotFoundException
     * @throws MultipleInstancesOfAggregateDetectedException
     */
    public function handle(TitleWasChanged $command): void
    {
        $post = $this->aggregateRepository->loadAggregateRoot(
            PostId::fromString(postId: $command->postId()->__toString())
        );
        $post->changeTitle(title: $command->title());

        $this->aggregateRepository->saveAggregateRoot(aggregate: $post);
    }
}
