<?php

declare(strict_types=1);

namespace Codefy\Tests;

use Codefy\Domain\Aggregate\AggregateId;
use Codefy\Domain\EventSourcing\AggregateChanged;
use Codefy\Domain\EventSourcing\DomainEvent;
use Codefy\Domain\Metadata;
use Qubus\Exception\Data\TypeException;

use function Qubus\Support\Helpers\is_null__;

final class PostWasCreated extends AggregateChanged implements DomainEvent
{
    private PostId $postId;

    private Title $title;

    private Content $content;

    public static function withData(
        PostId $postId,
        Title $title,
        Content $content
    ): PostWasCreated|DomainEvent|AggregateChanged {
        $event = self::occur(
            aggregateId: $postId,
            payload: [
                'title' => $title,
                'content' => $content,
            ],
            metadata: [
                Metadata::AGGREGATE_TYPE => 'post'
            ]
        );

        $event->postId = $postId;
        $event->title = $title;
        $event->content = $content;

        return $event;
    }

    /**
     * @throws TypeException
     */
    public function postId(): PostId|AggregateId
    {
        if (is_null__($this->postId)) {
            $this->postId = PostId::fromString(postId: $this->aggregateId()->__toString());
        }

        return $this->postId;
    }

    /**
     * @throws TypeException
     */
    public function title(): Title
    {
        if (is_null__($this->title)) {
            $this->title = Title::fromString(title: $this->payload()['title']->__toString());
        }

        return $this->title;
    }

    /**
     * @throws TypeException
     */
    public function content(): Content
    {
        if (is_null__($this->content)) {
            $this->content = Content::fromString(content: $this->payload()['content']->__toString());
        }

        return $this->content;
    }
}
