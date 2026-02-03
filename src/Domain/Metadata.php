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
    public const string EVENT_ID = '__event_id';

    public const string EVENT_TYPE = '__event_type';

    public const string RECORDED_AT = '__recorded_at';

    public const string AGGREGATE_ID = '__aggregate_id';

    public const string AGGREGATE_TYPE = '__aggregate_type';

    public const string AGGREGATE_PLAYHEAD = '__aggregate_playhead';
}
