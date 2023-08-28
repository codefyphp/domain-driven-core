<?php

declare(strict_types=1);

namespace Codefy\Tests;

use Codefy\CommandBus\Command;
use Codefy\CommandBus\CommandHandler;
use Codefy\CommandBus\PayloadCommand;

class SelfHandlingCommand extends PayloadCommand implements Command, CommandHandler
{
    public const PRODUCT_ID = 'productId';
    public const PRODUCT_NAME = 'productName';

    protected static array $REQUIRED_FIELDS = [
        self::PRODUCT_ID,
        self::PRODUCT_NAME,
    ];

    public function __construct(array $payload = [])
    {
        parent::__construct(payload: $payload);
    }

    public function productId(): string
    {
        return $this->get(key: self::PRODUCT_ID);
    }

    public function productName(): string
    {
        return $this->get(key: self::PRODUCT_NAME);
    }

    public function handle(Command $command)
    {
        $command->productName();
    }
}
