<?php

declare(strict_types=1);

namespace Codefy\Tests\CommandBus;

use Codefy\CommandBus\PayloadCommand;

class CreatePostCommand extends PayloadCommand
{
    public const POST_ID = 'postId';
    public const POST_TITLE = 'postTitle';
    public const POST_CONTENT = 'postContent';

    protected static array $REQUIRED_FIELDS = [
        self::POST_ID,
        self::POST_TITLE,
    ];

    protected static array $ALLOWED_FIELDS = [
        self::POST_CONTENT,
    ];

    public function postId(): ?string
    {
        return $this->get(key: self::POST_ID);
    }

    public function title(): ?string
    {
        return $this->get(key: self::POST_TITLE);
    }

    public function content(): ?string
    {
        return $this->get(key: self::POST_CONTENT);
    }
}
