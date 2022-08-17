<?php

/**
 * CodefyPHP
 *
 * @link       https://github.com/codefyphp/codefy
 * @copyright  2022 Joshua Parker <josh@joshuaparker.blog>
 * @copyright  2015 Adam Nicholson
 * @license    https://opensource.org/licenses/mit-license.php MIT License
 *
 * @since      1.0.0
 */

declare(strict_types=1);

namespace Codefy\Tests\CommandBus;

use Codefy\EventBus\EventBus;
use Codefy\Tests\Domain\Content;
use Codefy\Tests\Domain\InMemoryPostProjection;
use Codefy\Tests\Domain\Post;
use Codefy\Tests\Domain\PostId;
use Codefy\Tests\Domain\PostSubscriber;
use Codefy\Tests\Domain\Title;
use Qubus\Exception\Data\TypeException;

class PostEventBusCommandHandler
{
    public function __construct(public readonly EventBus $eventBus)
    {
        $this->eventBus->subscribe(
            subscriber: new PostSubscriber(
                projection: new InMemoryPostProjection()
            )
        );
    }

    /**
     * @throws TypeException
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