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
}
