<?php

/**
 * CodefyPHP
 *
 * @link       https://github.com/codefyphp/codefy
 * @copyright  2022 Joshua Parker <josh@joshuaparker.blog>
 * @license    https://opensource.org/licenses/mit-license.php MIT License
 *
 * @since      1.0.0
 */

declare(strict_types=1);

namespace Codefy\Tests;

use Codefy\Domain\Aggregate\AggregateId;
use Codefy\Domain\EventSourcing\AggregateChanged;
use Codefy\Domain\EventSourcing\DomainEvent;
use Codefy\Tests\Domain\PostId;
use PHPUnit\Framework\Assert;

use function it;

it('should have AggregateId after construct.', function () {
    $event = AggregateChanged::occur(
        aggregateId: new PostId(value: 'a5a72aa5-bf79-469a-b213-b8a8fa3189ca'),
        payload: []
    );

    Assert::assertInstanceOf(expected: AggregateId::class, actual: $event->aggregateId());
});

it('should track aggregate playhead but is immutable.', function () {
    $originalEvent = AggregateChanged::occur(
        aggregateId: new PostId(value: 'a5a72aa5-bf79-469a-b213-b8a8fa3189ca'),
        payload: ['title' => 'Is Immutable']
    );

    $newEvent = $originalEvent->withPlayhead(playhead: 2);

    Assert::assertEquals(expected: 1, actual: $originalEvent->playhead());
    Assert::assertEquals(expected: 2, actual: $newEvent->playhead());
});

it('should reference an aggregate.', function () {
    $event = AggregateChanged::occur(
        aggregateId: new PostId(value: 'a5a72aa5-bf79-469a-b213-b8a8fa3189ca'),
        payload: []
    );
    Assert::assertEquals(expected: 'a5a72aa5-bf79-469a-b213-b8a8fa3189ca', actual: $event->aggregateId());
});

it('can be assigned payload after construct.', function () {
    $payload = ['test'];

    $event = AggregateChanged::occur(
        aggregateId: new PostId(value: 'a5a72aa5-bf79-469a-b213-b8a8fa3189ca'),
        payload: $payload
    );

    Assert::assertEquals(expected: $payload, actual: $event->payload());
});

it('should return a domain event from array.', function () {
    $data = [
        'aggregateId' => new PostId('a5a72aa5-bf79-469a-b213-b8a8fa3189ca'),
        'payload' => [],
        'metadata' => [],
    ];

    $event = AggregateChanged::fromArray($data);

    Assert::assertInstanceOf(expected: DomainEvent::class, actual: $event);
});

it('should return with metadata.', function () {
    $data = [
        'aggregateId' => new PostId('a5a72aa5-bf79-469a-b213-b8a8fa3189ca'),
        'payload' => [],
        'metadata' => [],
    ];

    $event = AggregateChanged::occur(
        aggregateId: new PostId(value: 'a5a72aa5-bf79-469a-b213-b8a8fa3189ca'),
        payload: $data
    )->withMetadata(metadata: ['test' => 'metadata']);

    Assert::assertEquals(expected: 'metadata', actual: $event->metaParam(name: 'test'));
    Assert::assertInstanceOf(expected: DomainEvent::class, actual: $event);
});

it('should return with added metadata.', function () {
    $data = [
        'aggregateId' => new PostId('a5a72aa5-bf79-469a-b213-b8a8fa3189ca'),
        'payload' => [],
    ];

    $event = AggregateChanged::occur(
        aggregateId: new PostId(value: 'a5a72aa5-bf79-469a-b213-b8a8fa3189ca'),
        payload: $data
    )->withAddedMetadata(key: 'test', value: 'metadata');

    Assert::assertEquals(expected: 'metadata', actual: $event->metaParam(name: 'test'));
    Assert::assertInstanceOf(expected: DomainEvent::class, actual: $event);
});
