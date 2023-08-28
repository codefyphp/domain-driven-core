<?php

declare(strict_types=1);

namespace Codefy\Tests;

use Codefy\Domain\Aggregate\AggregateId;
use Codefy\Domain\Aggregate\AggregateRoot;
use Codefy\Domain\Aggregate\AggregateRootFactory;

final class PostFactory implements AggregateRootFactory
{
    /**
     * Returns a post object.
     *
     * @param AggregateId $aggregateId
     * @return Post|AggregateRoot
     */
    public function create(AggregateId $aggregateId): Post|AggregateRoot
    {
        return Post::fromNative(postId: $aggregateId);
    }
}
