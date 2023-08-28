<?php

declare(strict_types=1);

namespace Codefy\Tests;

use Codefy\Domain\Aggregate\AggregateId;
use Qubus\Exception\Data\TypeException;
use Qubus\ValueObjects\Identity\Uuid;

final class PostId extends Uuid implements AggregateId
{
    /**
     * @throws TypeException
     */
    public static function fromString(string $postId): self
    {
        return new self(value: $postId);
    }

    public function aggregateClassName(): string
    {
        return Post::className();
    }
}
