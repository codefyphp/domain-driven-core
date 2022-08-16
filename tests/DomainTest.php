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
use Codefy\Domain\Aggregate\EventStream;
use Codefy\Domain\EventSourcing\DomainEvent;
use Codefy\Domain\EventSourcing\EventId;
use Codefy\Domain\EventSourcing\InMemoryEventStore;
use Codefy\Domain\Metadata;
use Codefy\Tests\Domain\Content;
use Codefy\Tests\Domain\InMemoryPostProjection;
use Codefy\Tests\Domain\Post;
use Codefy\Tests\Domain\PostFactory;
use Codefy\Tests\Domain\PostId;
use Codefy\Tests\Domain\PostRepository;
use Codefy\Tests\Domain\PostWasCreated;
use Codefy\Tests\Domain\Title;
use Codefy\Tests\Domain\TitleWasChanged;
use PHPUnit\Framework\Assert;
use Qubus\Exception\Data\TypeException;
use Qubus\Support\DateTime\QubusDateTimeImmutable;
use Qubus\Support\DateTime\QubusDateTimeZone;

use function expect;
use function it;
use function iterator_to_array;

try {
    $postId = PostId::fromNative('760b7c16-b28e-4d31-9f93-7a2f0d3a1c51');
} catch (TypeException $e) {
    return $e;
}

$aggregateRootId = new PostId(value: '1cf57c2c-5c82-45a0-8a42-f0b725cfc42f');
$eventId = new EventId(value: 'bef2bce0-c690-415c-9901-1b26db1928d3');

$eventWithData = TitleWasChanged::withData(
    postId: $aggregateRootId,
    title: new Title('Aggregate Changed Title')
)->withAddedMetadata(key: Metadata::EVENT_ID, value: $eventId);

$eventFromArray = TitleWasChanged::fromArray(
    data: [
        'eventId' => $eventId,
        'eventType' => 'title-was-changed',
        'aggregateId' => $aggregateRootId,
        'payload' => [
            'title' => 'Aggregate Changed Title',
        ],
        'metadata' => [
            Metadata::AGGREGATE_ID => $aggregateRootId,
            Metadata::AGGREGATE_TYPE => 'post',
            Metadata::EVENT_ID => $eventId,
            Metadata::EVENT_TYPE => 'title-was-changed',
        ],
        'recordedAt' => new QubusDateTimeImmutable(time: 'now', tz: new QubusDateTimeZone(timezone: 'UTC')),
    ]
)
    ->withPlayhead(playhead: 5);

it(description: 'should be an instance of AggregateId.', closure: function () use ($postId) {
    Assert::assertInstanceOf(expected: AggregateId::class, actual: $postId);
});

it(description: 'should be cast to a string.', closure: function () use ($postId) {
    Assert::assertEquals(expected: '760b7c16-b28e-4d31-9f93-7a2f0d3a1c51', actual: (string)$postId);
});

it('should equal instances of the same type and value.', function () use ($postId) {
    $id = PostId::fromNative($postId->toNative());

    expect(value: $postId)->toEqual(expected: $id);
});

it('should not equal instances of the same type and value.', function () use ($postId) {
    $id = PostId::fromNative('d5f77025-63d5-43a1-966e-cfe4d576ebd5');

    expect(value: $postId)->not()->toEqual(expected: $id);
});

/**
 * Testing a domain event.
 */
$event = PostWasCreated::withData(
    postId: $postId,
    title: new Title(value: 'New Post Title'),
    content: new Content(value: 'Short content for this new post.')
);

it('should be an instance of DomainEvent', function () use ($event) {
    Assert::assertInstanceOf(expected: DomainEvent::class, actual: $event);
});

it('should equal another instance with the same value.', function () use ($event) {
    expect(value: $event->aggregateId())->toEqual(expected: PostId::fromNative('760b7c16-b28e-4d31-9f93-7a2f0d3a1c51'));
});

it('should expose a title.', function () use ($event) {
    expect(value: $event->title())->toEqual(expected: new Title(value: 'New Post Title'));
});

it('should expose content.', function () use ($event) {
    expect(value: $event->content())->toEqual(expected: new Content(value: 'Short content for this new post.'));
});

/**
 * Testing recorded events.
 */
it('should have recorded 2 events.', function () {
    $post = Post::createPostWithoutTap(
        postId: new PostId(value: '760b7c16-b28e-4d31-9f93-7a2f0d3a1c51'),
        title: new Title(value: 'New Post Title'),
        content: new Content(value: 'This is short form content.')
    );
    $post->changeTitle(title: new Title(value: 'Updated Post Title'));
    $events = $post->getRecordedEvents();

    expect(value: PostId::fromString('760b7c16-b28e-4d31-9f93-7a2f0d3a1c51'))->toEqual(expected: $post->aggregateId())
        ->and(value: 2)->toEqual(expected: $post->playhead());
    Assert::assertCount(expectedCount: 2, haystack: $events);
});

/**
 * Testing EventStream.
 */
it('should be the same after reconstitution.', function () {
    $postId = new PostId(value: '1cf57c2c-5c82-45a0-8a42-f0b725cfc42f');
    $post = Post::createPostWithoutTap(
        postId: $postId,
        title: new Title(value: 'Second Post Title'),
        content: new Content(value: 'Another short form content.')
    );
    $post->changeTitle(title: new Title(value: 'Reconstitute From History'));
    $events = $post->getRecordedEvents();
    $post->clearRecordedEvents();

    $reconstitutedPost = Post::reconstituteFromEventStream(
        aggregateHistory: new EventStream(
            aggregateId: $postId,
            events: iterator_to_array(
                iterator: $events->getIterator()
            )
        )
    );

    expect(value: $post)->toEqual(expected: $reconstitutedPost);
});

it('should behave the same after reconstitution.', function () {
    $postId = new PostId(value: '1cf57c2c-5c82-45a0-8a42-f0b725cfc42f');

    $post = Post::createPostWithoutTap(
        postId: $postId,
        title: new Title(value: 'Second Post Title'),
        content: new Content(value: 'Another short form content.')
    );
    $post->changeTitle(title: new Title(value: 'Reconstitute From History'));
    $history = $post->getRecordedEvents();
    $post->clearRecordedEvents();

    $reconstitutedPost = Post::reconstituteFromEventStream(
        aggregateHistory: new EventStream(
            aggregateId: $postId,
            events: iterator_to_array(
                iterator: $history->getIterator()
            )
        )
    );
    $reconstitutedPost->changeTitle(title: new Title(value: 'Reconstituted Title'));

    if (!$reconstitutedPost->hasRecordedEvents()) {
        Assert::assertFalse(
            condition: $reconstitutedPost->hasRecordedEvents(),
            message: 'There are no recorded events.'
        );
    }

    $changes = $reconstitutedPost->getRecordedEvents();
    $array = iterator_to_array(iterator: $changes);

    Assert::assertInstanceOf(expected: TitleWasChanged::class, actual: $array[0]);
    Assert::assertCount(expectedCount: 1, haystack: $changes);
});

/**
 * Testing InMemory EventStore.
 */
it('should reconstitute a Post without tap() to its state after persisting it.', function () {
    $postId = new PostId(value: '1cf57c2c-5c82-45a0-8a42-f0b725cfc42f');

    $post = Post::createPostWithoutTap(
        postId: $postId,
        title: new Title(value: 'Second Post Title'),
        content: new Content(value: 'Another short form content.')
    );

    $posts = new PostRepository(eventStore: new InMemoryEventStore(), projection: new InMemoryPostProjection());
    $posts->save(aggregate: $post);
    $reconstitutedPost = $posts->find(aggregateId: $postId);

    expect(value: $post)->toEqual(expected: $reconstitutedPost);
});

it('should reconstitute a Post with tap() to its state after persisting it.', function () {
    $postId = new PostId(value: '1cf57c2c-5c82-45a0-8a42-f0b725cfc42f');

    $post = Post::createPostWithTap(
        postId: $postId,
        title: new Title(value: 'Second Post Title'),
        content: new Content(value: 'Another short form content.')
    );

    $posts = new PostRepository(eventStore: new InMemoryEventStore(), projection: new InMemoryPostProjection());
    $posts->save(aggregate: $post);
    $reconstitutedPost = $posts->find(aggregateId: $postId);

    expect(value: $post)->toEqual(expected: $reconstitutedPost);
});

/**
 * Testing an aggregate factory.
 */
it('should be the same Post object when using PostFactory.', function () {
    $postId = new PostId(value: '1cf57c2c-5c82-45a0-8a42-f0b725cfc42f');

    $post = (new PostFactory())->create(aggregateId: $postId);

    Assert::assertInstanceOf(expected: Post::class, actual: $post);
});

it('should return domain event with data.', function () use ($eventWithData, $eventId) {
    expect(value: $eventWithData->eventType())->toEqual(expected: 'title-was-changed')
        ->and(value: $eventWithData->metaParam(name: '__event_type'))->toEqual(expected: 'title-was-changed')
        ->and(value: $eventWithData->playhead())->toEqual(expected: 1)
        ->and(value: $eventWithData->metaParam(name: '__aggregate_playhead'))->toEqual(expected: 1)
        ->and(value: 'post')->toEqual(expected: $eventWithData->metaParam(name: '__aggregate_type'))
        ->and(value: $eventWithData->aggregateId()->__toString())->toEqual(
            expected: '1cf57c2c-5c82-45a0-8a42-f0b725cfc42f'
        )
        ->and(value: $eventWithData->payload()['title'])->toEqual(expected: 'Aggregate Changed Title')
        ->and(value: $eventWithData->metadata())->toEqual(expected: $eventWithData->toArray()['metadata'])
        ->and(value: $eventWithData->eventId())->toEqual(
            expected: EventId::fromNative($eventWithData->eventId()->__toString())
        )
        ->and(value: $eventWithData->param(name: 'title'))->toEqual(expected: 'Aggregate Changed Title')
        ->and(value: $eventWithData->metaParam(name: '__aggregate_id'))->toEqual(
            expected: '1cf57c2c-5c82-45a0-8a42-f0b725cfc42f'
        )
        ->and(value: $eventId->__toString())->toEqual(
            expected: EventId::fromString(
                eventId: $eventWithData->metaParam(name: '__event_id')->__toString()
            )->__toString()
        )
        ->and(value: $eventWithData->recordedAt())->toEqual($eventWithData->metaParam(name: '__recorded_at'));
});

it('should return domain event from array.', function () use ($eventFromArray, $eventId) {
    expect(value: $eventFromArray->eventType())->toEqual(expected: 'title-was-changed')
        ->and(value: $eventFromArray->playhead())->toEqual(expected: 5)
        ->and(value: $eventFromArray->aggregateId()->__toString())->toEqual(
            expected: '1cf57c2c-5c82-45a0-8a42-f0b725cfc42f'
        )
        ->and(value: $eventFromArray->payload()['title'])->toEqual(expected: 'Aggregate Changed Title')
        ->and(value: $eventFromArray->metadata())->toEqual(expected: $eventFromArray->toArray()['metadata'])
        ->and(value: $eventFromArray->eventId()->__toString())->toEqual(
            expected: EventId::fromNative($eventFromArray->eventId()->__toString())
        )
        ->and(value: $eventFromArray->param(name: 'title'))->toEqual(expected: 'Aggregate Changed Title')
        ->and(value: $eventFromArray->metaParam(name: '__aggregate_id'))->toEqual(
            expected: '1cf57c2c-5c82-45a0-8a42-f0b725cfc42f'
        )
        ->and($eventFromArray->toArray())->toEqual(
            expected: [
                'eventType' => 'title-was-changed',
                'payload' => [
                    'title' => 'Aggregate Changed Title',
                ],
                'metadata' => [
                    '__aggregate_id' => new PostId(value: '1cf57c2c-5c82-45a0-8a42-f0b725cfc42f'),
                    '__aggregate_playhead' => 5,
                    '__event_id' => new EventId(value: $eventId->__toString()),
                    '__event_type' => 'title-was-changed',
                    '__aggregate_type' => 'post',
                    '__recorded_at' => $eventFromArray->recordedAt(),
                ],
                'recordedAt' => $eventFromArray->recordedAt(),
                'eventId' => new EventId(value: $eventId->__toString()),
            ]
        );
});

it(
    'should return `__aggregate_id` value: 1cf57c2c-5c82-45a0-8a42-f0b725cfc42f.',
    function () use ($eventFromArray) {
        expect(value: $eventFromArray->metaParam(name: '__aggregate_id'))->toEqual(
            expected: '1cf57c2c-5c82-45a0-8a42-f0b725cfc42f'
        );
    }
);

it('should return `__aggregate_type` value: post.', function () use ($eventFromArray) {
    expect(value: $eventFromArray->metaParam(name: '__aggregate_type'))->toEqual(
        expected: 'post'
    );
});

it('should return `__aggregate_playhead` value: 1.', function () use ($eventFromArray) {
    expect(value: $eventFromArray->metaParam(name: '__aggregate_playhead'))->toEqual(
        expected: 5
    );
});

it(sprintf('should return `__event_id` value: %s.', $eventId->__toString()), function () use ($eventFromArray) {
    expect(value: (new EventId($eventFromArray->eventId()->__toString()))->__toString())->toEqual(
        expected: $eventFromArray->metaParam(name: '__event_id')
    );
});

it('should return `__event_type` value: title-was-changed.', function () use ($eventFromArray) {
    expect(value: 'title-was-changed')->toEqual(expected: $eventFromArray->metaParam(name: '__event_type'))
        ->and(value: 'title-was-changed')->toEqual(expected: $eventFromArray->eventType());
});

it('should return expected occuredOn value.', function () use ($eventFromArray) {
    expect(value: $eventFromArray->recordedAt())->toEqual(expected: $eventFromArray->metaParam(name: '__recorded_at'));
});

it('should generate eventType automatically to be: title-was-changed.', function () {
    $event = TitleWasChanged::withData(
        postId: new PostId(),
        title: new Title(
            value: 'Automatically Generate Event Type'
        )
    );
    expect(value: $event->eventType())->toEqual(expected: 'title-was-changed')
        ->and(value: $event->metaParam(name: '__event_type'))->toEqual(expected: 'title-was-changed');
});
