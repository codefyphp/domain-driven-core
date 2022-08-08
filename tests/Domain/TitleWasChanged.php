<?php

/**
 * CodefyPHP
 *
 * @link       https://github.com/codefyphp/codefy
 * @copyright  2022 Joshua Parker <josh@joshuaparker.blog>
 * @license    https://opensource.org/licenses/mit-license.php MIT License
 *
 * @since      1.0.0
 */

declare(strict_types=1);

namespace Codefy\Tests\Domain;

use Codefy\Domain\Aggregate\AggregateId;
use Codefy\Domain\EventSourcing\AggregateChanged;
use Codefy\Domain\EventSourcing\DomainEvent;
use Codefy\Domain\Metadata;
use Qubus\Exception\Data\TypeException;

use function Qubus\Support\Helpers\is_null__;

final class TitleWasChanged extends AggregateChanged implements DomainEvent
{
    private PostId $postId;

    private Title $title;

    public static function withData(PostId $postId, Title $title): DomainEvent|AggregateChanged
    {
        $event = self::occur(
            aggregateId: $postId,
            payload: [
                'title' => $title,
            ],
            metadata: [
                Metadata::AGGREGATE_TYPE => 'post'
            ]
        );

        $event->postId = $postId;
        $event->title = $title;

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
}
