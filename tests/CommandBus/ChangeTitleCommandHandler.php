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

use Codefy\Domain\Aggregate\AggregateRepository;
use Codefy\Tests\Domain\Content;
use Codefy\Tests\Domain\Post;
use Codefy\Tests\Domain\PostId;
use Codefy\Tests\Domain\Title;
use Codefy\Tests\Domain\TitleWasChanged;
use Qubus\Exception\Data\TypeException;

class ChangeTitleCommandHandler
{
    public function __construct(public readonly AggregateRepository $aggregateRepository)
    {
    }

    /**
     * @throws TypeException
     */
    public function handle(TitleWasChanged $command): void
    {
        $post = $this->aggregateRepository->find(
            PostId::fromString(postId: $command->postId()->__toString())
        );
        $post->changeTitle(title: $command->title());

        $this->aggregateRepository->save(aggregate: $post);
    }
}
