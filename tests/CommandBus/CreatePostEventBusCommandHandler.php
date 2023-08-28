<?php

declare(strict_types=1);

namespace Codefy\Tests;

use Codefy\EventBus\EventBus;
use Qubus\Exception\Data\TypeException;

readonly class CreatePostEventBusCommandHandler
{
    public function __construct(public EventBus $eventBus)
    {
        $this->eventBus->subscribe(
            subscriber: new PostSubscriber(
                projection: new InMemoryPostProjection()
            )
        );
    }

    /**
     * @throws TypeException|TitleWasNullException
     */
    public function handle(CreatePostCommand $command): void
    {
        $post = Post::createPostWithoutTap(
            postId: new PostId($command->postId()),
            title: new Title($command->title()),
            content: new Content($command->content())
        );

        $this->eventBus->publish(...$post->pullDomainEvents());

        $post->clearRecordedEvents();
    }
}
