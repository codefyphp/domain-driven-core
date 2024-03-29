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
use Qubus\ValueObjects\StringLiteral\StringLiteral;
use ReflectionClass;

use function Qubus\Support\Helpers\is_null__;

class EventName
{
    protected ?StringLiteral $name = null;

    public function __construct(public readonly DomainEvent $event)
    {
    }

    /**
     * @throws TypeException
     */
    public function __toString(): string
    {
        if (is_null__($this->name)) {
            $this->name = $this->parseName();
        }

        return $this->name->__toString();
    }

    /**
     * @throws TypeException
     */
    private function parseName(): StringLiteral
    {
        $className = (new ReflectionClass($this->event))->getShortName();

        return new StringLiteral($className);
    }
}
