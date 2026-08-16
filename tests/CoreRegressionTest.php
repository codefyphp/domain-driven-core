<?php

declare(strict_types=1);

use Codefy\CommandBus\Command;
use Codefy\CommandBus\CommandBus;
use Codefy\CommandBus\Containers\NativeContainer;
use Codefy\CommandBus\Decorators\TransactionalCommandLockingDecorator;
use Codefy\Domain\EventSourcing\InMemoryEventStore;
use Codefy\EventBus\GenericPublisher;
use Codefy\QueryBus\Busses\SynchronousQueryBus;
use Codefy\QueryBus\Resolvers\NativeQueryHandlerResolver;
use Codefy\Tests\Domain\Content;
use Codefy\Tests\Domain\PostId;
use Codefy\Tests\Domain\PostWasCreated;
use Codefy\Tests\Domain\Title;
use Codefy\Tests\Domain\TitleWasChanged;
use Codefy\Tests\Regression\CoreRegressionAlternateAggregate;
use Codefy\Tests\Regression\CoreRegressionAlternateId;
use Codefy\Tests\Regression\CoreRegressionCommand;
use Codefy\Tests\Regression\CoreRegressionConsumer;
use Codefy\Tests\Regression\CoreRegressionCounter;
use Codefy\Tests\Regression\CoreRegressionDependency;
use Codefy\Tests\Regression\CoreRegressionIdentityMap;
use Codefy\Tests\Regression\CoreRegressionQuery;
use Codefy\Tests\Regression\CoreRegressionQueryHandler;
use Codefy\Tests\Regression\CoreRegressionReentrantBus;
use Codefy\Tests\Regression\CoreRegressionSubscriber;

it('binds callable and lazy query handlers without rejecting valid handlers', function () {
    $query = new CoreRegressionQuery('resolved');

    $callableResolver = new NativeQueryHandlerResolver();
    $callableResolver->bindHandler(CoreRegressionQuery::class, fn (CoreRegressionQuery $query) => $query->value);

    expect(new SynchronousQueryBus($callableResolver)->execute($query))->toBe('resolved');

    $lazyResolver = new NativeQueryHandlerResolver();
    $lazyResolver->bindHandler(CoreRegressionQuery::class, CoreRegressionQueryHandler::class);

    expect(new SynchronousQueryBus($lazyResolver)->execute($query))->toBe('resolved');
});

it('autowires native container dependencies by declared type', function () {
    $consumer = new NativeContainer()->make(CoreRegressionConsumer::class);

    expect($consumer)->toBeInstanceOf(CoreRegressionConsumer::class)
        ->and($consumer->dependency)->toBeInstanceOf(CoreRegressionDependency::class);
});

it('unlocks a transactional command bus after an exception', function () {
    $innerBus = new class () implements CommandBus {
        public int $attempts = 0;

        public function execute(Command $command): mixed
        {
            if (++$this->attempts === 1) {
                throw new RuntimeException('failed');
            }

            return 'recovered';
        }
    };
    $bus = new TransactionalCommandLockingDecorator($innerBus);

    try {
        $bus->execute(new CoreRegressionCommand('first'));
    } catch (RuntimeException) {
    }

    expect($bus->execute(new CoreRegressionCommand('second')))->toBe('recovered');
});

it('drains nested transactional commands exactly once', function () {
    $innerBus = new CoreRegressionReentrantBus();
    $bus = new TransactionalCommandLockingDecorator($innerBus);
    $innerBus->outerBus = $bus;

    $bus->execute(new CoreRegressionCommand('outer'));
    $bus->execute(new CoreRegressionCommand('later'));

    expect($innerBus->calls)->toBe(['outer', 'nested', 'later']);
});

it('applies each new event once and publishes its assigned playhead', function () {
    $subscriber = new CoreRegressionSubscriber();
    GenericPublisher::instance()->subscribe($subscriber);
    $counter = CoreRegressionCounter::create(new PostId());

    $counter->increment();
    $counter->increment();

    expect($counter->countValue())->toBe(2)
        ->and($subscriber->events)->toHaveCount(2)
        ->and($subscriber->events[0]->playhead())->toBe(1)
        ->and($subscriber->events[1]->playhead())->toBe(2);
});

it('compares aggregate identity by value', function () {
    $id = new PostId();
    $first = CoreRegressionCounter::create($id);
    $second = CoreRegressionCounter::create(new PostId($id->__toString()));

    expect($first->equals($second))->toBeTrue();
});

it('isolates identity map entries by aggregate type and id', function () {
    $id = new PostId();
    $alternateId = new CoreRegressionAlternateId($id->__toString());
    $first = CoreRegressionCounter::create($id);
    $second = CoreRegressionAlternateAggregate::create($alternateId);
    $identityMap = new CoreRegressionIdentityMap();

    $identityMap->attachToIdentityMap($first);
    $identityMap->attachToIdentityMap($second);

    expect($identityMap->retrieveFromIdentityMap($id))->toBe($first)
        ->and($identityMap->retrieveFromIdentityMap($alternateId))->toBe($second);
});

it('round trips event arrays and preserves their recorded timestamp', function () {
    $event = TitleWasChanged::withData(new PostId(), new Title('Round trip'));
    $restored = TitleWasChanged::fromArray($event->toArray());

    expect($restored->aggregateId())->toEqual($event->aggregateId())
        ->and($restored->eventId())->toEqual($event->eventId())
        ->and($restored->eventType())->toBe($event->eventType())
        ->and($restored->recordedAt())->toBe($event->recordedAt());
});

it('reindexes filtered in-memory event streams', function () {
    $eventStore = new InMemoryEventStore();
    $firstId = new PostId();
    $secondId = new PostId();
    $eventStore->append(PostWasCreated::withData($firstId, new Title('First'), new Content('First')));
    $secondEvent = PostWasCreated::withData($secondId, new Title('Second'), new Content('Second'));
    $eventStore->append($secondEvent);

    expect($eventStore->getAggregateHistoryFor($secondId)->getFirstEvent())->toBe($secondEvent);
});
