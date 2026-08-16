<?php

/**
 * CodefyPHP
 *
 * @link       https://github.com/codefyphp/domain-driven-core
 * @copyright  2022 Joshua Parker <joshua@joshuaparker.dev>
 * @copyright  2015 Adam Nicholson <adamnicholson10@gmail.com>
 * @license    https://opensource.org/licenses/mit-license.php MIT License
 */

declare(strict_types=1);

namespace Codefy\CommandBus\Decorators;

use Codefy\CommandBus\Busses\SynchronousCommandBus;
use Codefy\CommandBus\CacheableCommand;
use Codefy\CommandBus\Command;
use Codefy\CommandBus\CommandBus;
use Codefy\CommandBus\Decorator;
use Codefy\CommandBus\Exceptions\UnresolvableCommandHandlerException;
use Codefy\CommandBus\HasCacheOptions;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Cache\InvalidArgumentException;
use ReflectionException;

use function hash;
use function serialize;

class CachingDecorator implements Decorator
{
    //phpcs:disable
    public function __construct(
        protected CacheItemPoolInterface $cache
        {
            get => $this->cache;
            set(CacheItemPoolInterface $value) => $this->cache = $value;
        },
        protected int $expiresAfter = 3600 {
            get => $this->expiresAfter;
            set(int $value) => $this->expiresAfter = $value;
        },
        protected CommandBus $innerBus = new SynchronousCommandBus() {
            get => $this->innerBus;
            set(CommandBus $value) => $this->innerBus = $value;
        }
    ) {
    }
    //phpcs:enable

    /**
     * @inheritDoc
     */
    public function setInnerBus(CommandBus $bus): void
    {
        $this->innerBus = $bus;
    }

    /**
     * @inheritDoc
     * @param Command $command
     * @return mixed
     * @throws InvalidArgumentException
     * @throws UnresolvableCommandHandlerException
     * @throws ReflectionException
     */
    public function execute(Command $command): mixed
    {
        if (!$command instanceof CacheableCommand) {
            return $this->innerBus->execute(command: $command);
        }

        $cached = $this->cache->getItem(key: $this->getCacheKey(command: $command));
        if ($cached->isHit()) {
            return $cached->get();
        }

        $value = $this->innerBus->execute(command: $command);

        $this->cache->save(item: $this->createCacheItem(command: $command, value: $value));

        return $value;
    }

    /**
     * Create a new cache item to be persisted.
     *
     * @throws InvalidArgumentException
     */
    private function createCacheItem(CacheableCommand $command, mixed $value): CacheItemInterface
    {
        return $this->cache->getItem(key: $this->getCacheKey(command: $command))
            ->expiresAfter(time: $this->getCacheExpiry(command: $command))
            ->set(value: $value);
    }

    /**
     * Create the key to be used when saving this item to the cache pool.
     *
     * The cache item key is taken as a (string) serialized command, to ensure the return value is unique
     * depending on the command properties; that serialized string is then hashed to ensure it doesn't
     * overflow any string length limits the implementing CacheItemPoolInterface library has.
     */
    private function getCacheKey(CacheableCommand $command): string
    {
        if ($command instanceof HasCacheOptions && $command->getCacheKey()) {
            return $command->getCacheKey();
        }

        return hash(algo: 'sha256', data: serialize(value: $command));
    }

    /**
     * Determine when this CachableCommand should expire, in terms of seconds from now.
     */
    private function getCacheExpiry(CacheableCommand $command): int
    {
        if ($command instanceof HasCacheOptions && $command->getCacheExpiry() > 0) {
            return $command->getCacheExpiry();
        }

        return $this->expiresAfter;
    }
}
