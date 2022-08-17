<?php

/**
 * CodefyPHP
 *
 * @link       https://github.com/codefyphp/codefy
 * @copyright  2022 Joshua Parker <josh@joshuaparker.blog>
 * @copyright  2014 Mathias Verraes <mathias@verraes.net>
 * @license    https://opensource.org/licenses/mit-license.php MIT License
 *
 * @since      0.1.0
 */

declare(strict_types=1);

namespace Codefy\Domain\Aggregate;

use Codefy\Domain\EventSourcing\EventSourcingException;
use Qubus\Exception\Exception;

final class MultipleInstancesOfAggregateDetectedException extends Exception implements EventSourcingException
{
}
