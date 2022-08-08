<?php

/**
 * CodefyPHP
 *
 * @link       https://github.com/codefyphp/codefy
 * @copyright  2022 Joshua Parker <josh@joshuaparker.blog>
 * @copyright  2019 Beau Simensen <beau@dflydev.com>
 * @license    https://opensource.org/licenses/mit-license.php MIT License
 *
 * @since      1.0.0
 */

declare(strict_types=1);

namespace Codefy\CommandBus;

use Qubus\Exception\Data\TypeException;

use function array_map;
use function count;
use function implode;
use function sprintf;

class InvalidPayloadException extends TypeException
{
    public static function missingRequiredFields(string ...$fields): static
    {
        $string = implode(
            separator: ',',
            array: array_map(function ($field) {
                return sprintf('%s', $field);
            }, array: $fields)
        );

        $label = count($fields) === 1 ? 'field' : 'fields';

        return new static(sprintf('Payload is missing required %s: %s.', $label, $string));
    }
}
