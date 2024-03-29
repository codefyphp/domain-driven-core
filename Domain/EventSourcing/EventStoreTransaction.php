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

namespace Codefy\Domain\EventSourcing;

/**
 * Code originated at https://github.com/beberlei/litecqrs-php/
 */
final readonly class EventStoreTransaction implements Transactional
{
    public function __construct(
        public TransactionId $transactionId,
        public DomainEvents $eventStream,
        public array $committedEvents
    ) {
    }

    public function transactionId(): TransactionId
    {
        return $this->transactionId;
    }

    public function eventStream(): DomainEvents
    {
        return $this->eventStream;
    }

    /**
     * {@inheritDoc}
     */
    public function committedEvents(): array
    {
        return $this->committedEvents;
    }
}
