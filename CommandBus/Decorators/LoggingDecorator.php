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
use Codefy\CommandBus\Command;
use Codefy\CommandBus\CommandBus;
use Codefy\CommandBus\Decorator;
use Codefy\CommandBus\Traits\InnerBusAware;
use Psr\Log\LoggerInterface;
use Qubus\Exception\Exception;
use Stringable;

use function serialize;
use function sprintf;

class LoggingDecorator implements Decorator
{
    use InnerBusAware;

    protected LoggerInterface $logger;

    protected mixed $context;

    /**
     * @param mixed|null $context Something which is serializable that will be logged with
     * the command execution, such as the request/session information.
     */
    public function __construct(LoggerInterface $logger, mixed $context = null, ?CommandBus $innerCommandBus = null)
    {
        $this->logger = $logger;
        $this->context = $context;
        $this->setInnerBus(bus: $innerCommandBus ?: new SynchronousCommandBus());
    }

    /**
     * Execute a command.
     *
     * @throws Exception
     */
    public function execute(Command $command): mixed
    {
        $this->log(
            message: sprintf(
                format: 'Executing command [%s]',
                values: $command::class
            ),
            command: $command
        );

        try {
            $response = $this->innerCommandBus->execute(command: $command);
        } catch (Exception $e) {
            $message = sprintf(
                'Failed executing command [%s]. %s',
                $command::class,
                $this->createExceptionString(e: $e)
            );
            $this->log(message: $message, command: $command);
            throw $e;
        }

        $this->log(
            message: sprintf(
                'Successfully executed command [%s]',
                $command::class
            ),
            command: $command
        );

        return $response;
    }

    protected function log(string|Stringable $message, Command $command)
    {
        $context = $this->context ? serialize(value: $this->context) : null;
        $this->logger->debug(
            message: $message,
            context: ['Command' => serialize($command), 'Context' => $context]
        );
    }

    protected function createExceptionString(Exception $e): string
    {
        return sprintf(
            'Uncaught %s [%s] throw in %s on line %s. Stack trace: %s',
            $e::class,
            $e->getMessage(),
            $e->getFile(),
            $e->getLine(),
            $e->getTraceAsString()
        );
    }
}
