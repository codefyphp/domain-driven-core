<?php

/**
 * CodefyPHP
 *
 * @link       https://github.com/codefyphp/codefy
 * @copyright  2022 Joshua Parker <josh@joshuaparker.blog>
 * @copyright  2015 Adam Nicholson <adamnicholson10@gmail.com>
 * @license    https://opensource.org/licenses/mit-license.php MIT License
 *
 * @since      0.1.0
 */

declare(strict_types=1);

namespace Codefy\CommandBus;

interface HasCacheOptions extends CacheableCommand
{
    /**
     * In how many seconds from now should this cache item expire.
     * Return null to use the default value specified in the CachingDecorator.
     */
    public function getCacheExpiry(): int|null;

    /**
     * The cache key used when caching this object. Return null to
     * automatically generate a cache key.
     */
    public function getCacheKey(): string|null;
}
