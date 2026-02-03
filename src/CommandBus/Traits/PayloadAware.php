<?php

/**
 * CodefyPHP
 *
 * @link       https://github.com/codefyphp/domain-driven-core
 * @copyright  2022 Joshua Parker <joshua@joshuaparker.dev>
 * @copyright  2019 Beau Simensen <beau@dflydev.com>
 * @license    https://opensource.org/licenses/mit-license.php MIT License
 */

declare(strict_types=1);

namespace Codefy\CommandBus\Traits;

use Codefy\CommandBus\InvalidPayloadException;
use Codefy\CommandBus\UndefinedValueException;
use Qubus\Exception\Exception;

use function array_diff;
use function array_keys;
use function array_merge;
use function count;
use function Qubus\Support\Helpers\array_key_exists__;
use function Qubus\Support\Helpers\is_null__;
use function sprintf;

trait PayloadAware
{
    /** @var array<mixed> $payload */
    private array $payload = [];

    /** @var array<mixed> $REQUIRED_FIELDS */
    protected static array $REQUIRED_FIELDS = [];

    /** @var array<mixed> $ALLOWED_FIELDS */
    protected static array $ALLOWED_FIELDS = [];

    /**
     * @param array<mixed> $payload
     */
    public function __construct(array $payload = [])
    {
        $this->payload = $payload;
    }

    /**
     * @param array<mixed> $payload
     * @return static
     */
    public static function fromPayload(array $payload): static
    {
        return new static($payload);
    }

    public function with(string $key, mixed $value = null): static
    {
        return new static(
            array_merge($this->payload, [
                $key => $value,
            ])
        );
    }

    public function without(string $key): static
    {
        $payload = $this->payload;
        unset($payload[$key]);

        return new static($payload);
    }

    public function get(string $key, mixed $default = null): mixed
    {
        if (!array_key_exists__($key, $this->payload)) {
            return $default;
        }

        return $this->payload[$key];
    }

    /**
     * @throws UndefinedValueException
     */
    public function getOrFail(string $key): mixed
    {
        $value = $this->get($key);

        if (is_null__($value)) {
            throw new UndefinedValueException(sprintf('Key `%s` does not have a defined value.', $key));
        }

        return $value;
    }

    /**
     * @return array<mixed>
     */
    public function payload(): array
    {
        return $this->payload;
    }

    /**
     * @param array<mixed> $payload
     * @throws InvalidPayloadException
     * @throws Exception
     * @return array<mixed>
     */
    public static function validate(array $payload = []): array
    {
        $payloadKeys = array_keys($payload);

        if (count($missingFields = array_diff(static::$REQUIRED_FIELDS, $payloadKeys)) > 0) {
            throw InvalidPayloadException::missingRequiredFields(...$missingFields);
        }

        $keysToRemove = array_diff($payloadKeys, static::$REQUIRED_FIELDS, static::$ALLOWED_FIELDS);
        foreach ($keysToRemove as $key) {
            unset($payload[$key]);
        }

        return $payload;
    }
}
