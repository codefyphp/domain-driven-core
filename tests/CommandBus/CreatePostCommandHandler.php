<?php

declare(strict_types=1);

namespace Codefy\Tests\CommandBus;

use Codefy\CommandBus\Command;
use Codefy\CommandBus\CommandHandler;
use Codefy\Domain\Aggregate\AggregateRepository;
use Codefy\Tests\Domain\Content;
use Codefy\Tests\Domain\Post;
use Codefy\Tests\Domain\PostId;
use Codefy\Tests\Domain\Title;
use Codefy\Tests\Domain\TitleWasNullException;
use Qubus\Exception\Data\TypeException;

readonly class CreatePostCommandHandler implements CommandHandler
{
    public function __construct(public AggregateRepository $aggregateRepository)
    {
    }

    /**
     * @throws TypeException|TitleWasNullException
     */
    public function handle(Command $command): void
    {
        $post = Post::createPostWithoutTap(
            postId: new PostId($command->postId()),
            title: new Title($command->title()),
            content: new Content($command->content())
        );

        $this->aggregateRepository->saveAggregateRoot(aggregate: $post);
    }
}
