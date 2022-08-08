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

namespace Codefy\Domain\EventSourcing;

use Qubus\Exception\Data\TypeException;
use Qubus\ValueObjects\Identity\Uuid;
use Qubus\ValueObjects\ValueObject;

class EventId extends Uuid implements ValueObject
{
    /**
     * @throws TypeException
     */
    public static function fromString(string $eventId): self
    {
        return new self(value: $eventId);
    }
}
