<?php

/**
 * CodefyPHP
 *
 * @link       https://github.com/codefyphp/codefy
 * @copyright  2022
 * @author     Joshua Parker <josh@joshuaparker.blog>
 * @license    https://opensource.org/licenses/mit-license.php MIT License
 *
 * @since      0.1.0
 */

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
