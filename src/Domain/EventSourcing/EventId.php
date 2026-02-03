<?php

/**
 * CodefyPHP
 *
 * @link       https://github.com/codefyphp/domain-driven-core
 * @copyright  2022
 * @author     Joshua Parker <joshua@joshuaparker.dev>
 * @license    https://opensource.org/licenses/mit-license.php MIT License
 */

declare(strict_types=1);

namespace Codefy\Domain\EventSourcing;

use Qubus\Exception\Data\TypeException;
use Qubus\ValueObjects\Identity\Ulid;
use Qubus\ValueObjects\ValueObject;

class EventId extends Ulid implements ValueObject
{
    /**
     * @throws TypeException
     */
    public static function fromString(string $eventId): self
    {
        return new self(value: $eventId);
    }
}
