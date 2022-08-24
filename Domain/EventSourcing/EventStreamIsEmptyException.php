<?php

/**
 * CodefyPHP
 *
 * @link       https://github.com/codefyphp/codefy
 * @copyright  2022
 * @author     Joshua Parker <josh@joshuaparker.blog>
 * @license    https://opensource.org/licenses/mit-license.php MIT License
 *
 * @since      0.2.0
 */

declare(strict_types=1);

namespace Codefy\Domain\EventSourcing;

use Qubus\Exception\Exception;

final class EventStreamIsEmptyException extends Exception implements EventSourcingException
{
}
