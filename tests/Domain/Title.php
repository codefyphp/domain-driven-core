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

use Qubus\Exception\Data\TypeException;
use Qubus\ValueObjects\StringLiteral\StringLiteral;

final class Title extends StringLiteral
{
    /**
     * @throws TypeException
     */
    public static function fromString(string $title): self
    {
        return new self(value: $title);
    }
}
