<?php

declare(strict_types=1);

namespace Codefy\Tests\Domain;

use Codefy\Domain\Aggregate\AggregateRoot;
use Codefy\Domain\Aggregate\EventSourcedAggregate;
use Qubus\Exception\Data\TypeException;

use function Qubus\Inheritance\Helpers\tap;

final class Post extends EventSourcedAggregate implements AggregateRoot
{
    private PostId $postId;

    private Title $title;

    private Content $content;

    /**
     * @throws TitleWasNullException
     */
    public static function createPostWithoutTap(PostId $postId, Title $title, Content $content): Post
    {
        if ($title->isEmpty()) {
            throw new TitleWasNullException(message: 'Title cannot be null.');
        }

        $post = self::root(aggregateId: $postId);

        $post->recordApplyAndPublishThat(
            event: PostWasCreated::withData($postId, $title, $content)
        );

        return $post;
    }

    /**
     * @throws TitleWasNullException
     */
    public static function createPostWithTap(PostId $postId, Title $title, Content $content): Post
    {
        if ($title->isEmpty()) {
            throw new TitleWasNullException(message: 'Title cannot be null.');
        }

        return tap(
            value: self::root($postId),
            callback: fn($post) => $post->recordApplyAndPublishThat(
                PostWasCreated::withData(postId: $postId, title: $title, content: $content)
            )
        );
    }

    public static function fromNative(PostId $postId): Post
    {
        return self::root(aggregateId: $postId);
    }

    /**S
     * @throws TitleWasNullException
     */
    public function changeTitle(Title $title): void
    {
        if ($title->isEmpty()) {
            throw new TitleWasNullException(message: 'Title cannot be null.');
        }
        if ($title->__toString() === $this->title->__toString()) {
            return;
        }
        $this->recordApplyAndPublishThat(
            event: TitleWasChanged::withData(postId: $this->postId, title: $title)
        );
    }

    public function title(): Title
    {
        return $this->title;
    }

    public function content(): Content
    {
        return $this->content;
    }

    /**
     * @throws TypeException
     */
    protected function whenPostWasCreated(PostWasCreated $event): void
    {
        $this->postId = $event->aggregateId();
        $this->title = $event->title();
        $this->content = $event->content();
    }

    /**
     * @throws TypeException
     */
    protected function whenTitleWasChanged(TitleWasChanged $event): void
    {
        $this->postId = $event->aggregateId();
        $this->title = $event->title();
    }
}
