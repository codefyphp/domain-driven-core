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
    public const AGGREGATE_VERSION = '__aggregate_version';
}
