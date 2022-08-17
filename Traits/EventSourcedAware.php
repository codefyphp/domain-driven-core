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

namespace Codefy\Traits;

use Codefy\Domain\Aggregate\EventStream;
use Codefy\Domain\Aggregate\AggregateId;
use Codefy\Domain\Aggregate\RecordsEvents;

trait EventSourcedAware
{
    use ReplayAware;
    use WhenAware;

    abstract public function aggregateId(): AggregateId;
}
