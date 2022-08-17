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

use Codefy\Domain\Aggregate\AggregateNotFoundException;
use Codefy\Domain\Aggregate\AggregateRepository;
use Codefy\Domain\Aggregate\MultipleInstancesOfAggregateDetectedException;
use Codefy\Tests\Domain\PostId;
use Codefy\Tests\Domain\TitleWasChanged;
use Qubus\Exception\Data\TypeException;

class ChangeTitleCommandHandler
{
    public function __construct(public readonly AggregateRepository $aggregateRepository)
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
