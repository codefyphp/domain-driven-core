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

namespace Codefy\Tests\CommandBus;

use Codefy\CommandBus\PayloadCommand;

class ChangeTitleCommand extends PayloadCommand
{
    public const POST_ID = 'postId';
    public const POST_TITLE = 'postTitle';

    protected static array $REQUIRED_FIELDS = [
        self::POST_ID,
        self::POST_TITLE,
    ];

    public function postId(): ?string
    {
        return $this->get(key: self::POST_ID);
    }

    public function title(): ?string
    {
        return $this->get(key: self::POST_TITLE);
    }
}
