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

namespace Codefy\Domain;

interface Metadata
{
    /** @var string Uuid of event. */
    public const EVENT_ID = '__event_id';
    /** @var string Name of the event. */
    public const EVENT_TYPE = '__event_type';
    /** @var string Datetime of when event was recorded. */
    public const RECORDED_AT = '__recorded_at';
    /** @var string Uuid of aggregate. */
    public const AGGREGATE_ID = '__aggregate_id';
    /** @var string Name of the aggregate. Usually class name. */
    public const AGGREGATE_TYPE = '__aggregate_type';
    /** @var int The version number of the aggregate. */
    public const AGGREGATE_PLAYHEAD = '__aggregate_playhead';
}
