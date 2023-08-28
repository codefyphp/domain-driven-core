<?php

declare(strict_types=1);

namespace Codefy\Tests;

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
