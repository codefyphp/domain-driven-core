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
final class EventStoreTransaction implements Transactional
{
    //phpcs:disable
    public function __construct(
        public TransactionId $transactionId {
            get => $this->transactionId;
            set(TransactionId $value) => $this->transactionId = $value;
        },
        public DomainEvents $eventStream {
            get => $this->eventStream;
            set(DomainEvents $value) => $this->eventStream = $value;
        },
        public array $committedEvents {
            get => $this->committedEvents;
            set(array $value) => $this->committedEvents = $value;
        }
    ) {
    }
    //phpcs:enable
}
