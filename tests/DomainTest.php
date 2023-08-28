<?php

declare(strict_types=1);

use Codefy\Domain\Aggregate\AggregateId;
use Codefy\Domain\EventSourcing\DomainEvent;
use Codefy\Domain\EventSourcing\DomainEvents;
use Codefy\Domain\EventSourcing\EventStream;
use Codefy\Domain\EventSourcing\InMemoryEventStore;
use Codefy\Domain\EventSourcing\Transactional;
use Codefy\Tests\Content;
use Codefy\Tests\InMemoryPostProjection;
use Codefy\Tests\Post;
use Codefy\Tests\PostFactory;
use Codefy\Tests\PostId;
use Codefy\Tests\PostRepository;
use Codefy\Tests\PostWasCreated;
use Codefy\Tests\Title;
use Codefy\Tests\TitleWasChanged;
use Codefy\Tests\TitleWasNullException;
use PHPUnit\Framework\Assert;
use Qubus\Exception\Data\TypeException;

try {
    $postId = PostId::fromNative('760b7c16-b28e-4d31-9f93-7a2f0d3a1c51');
} catch (TypeException $e) {
    return $e;
}

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

it('should not allow a null title.', function () use ($event) {
    return Post::createPostWithoutTap(
        postId: new PostId(value: '760b7c16-b28e-4d31-9f93-7a2f0d3a1c51'),
        title: new Title(value: ''),
        content: new Content(value: 'A null title is not allowed'),
    );
})->throws(exception: TitleWasNullException::class, exceptionMessage: 'Title cannot be null.');

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

    Assert::assertTrue(condition: $post->hasRecordedEvents());

    expect(value: PostId::fromString(postId: '760b7c16-b28e-4d31-9f93-7a2f0d3a1c51'))
        ->toEqual(expected: $post->aggregateId())
        ->and(value: 2)->toEqual(expected: $post->playhead());

    Assert::assertCount(expectedCount: 2, haystack: $events);

    $post->clearRecordedEvents();

    Assert::assertFalse(condition: $post->hasRecordedEvents());
    Assert::assertCount(expectedCount: 0, haystack: $post->getRecordedEvents());
});

it('should retrieve all events from the event store based on aggregate id.', function () {
    $postId = new PostId(value: '1cf57c2c-5c82-45a0-8a42-f0b725cfc42f');

    $post = Post::createPostWithoutTap(
        postId: $postId,
        title: new Title(value: 'Second Post Title'),
        content: new Content(value: 'Another short form content.')
    );

    $events = $post->getRecordedEvents();
    $eventStore = new InMemoryEventStore();

    foreach ($events as $event) {
        $eventStore->append(event: $event);
    }

    $iterator = $eventStore->getAggregateHistoryFor(
        aggregateId: PostId::fromString(
            postId: '1cf57c2c-5c82-45a0-8a42-f0b725cfc42f'
        )
    );
    $aggregateHistory = $iterator->toArray();

    foreach ($aggregateHistory as $event) {
        expect(value: $post->title())->toEqual(expected: $event->title());
        expect(value: $post->aggregateId())->toEqual(expected: $event->aggregateId());
    }
});

it('should return an event store transaction.', function () {
    $postId = new PostId(value: '1cf57c2c-5c82-45a0-8a42-f0b725cfc42f');

    $post = Post::createPostWithoutTap(
        postId: $postId,
        title: new Title(value: 'Second Post Title'),
        content: new Content(value: 'Another short form content.')
    );

    $events = $post->getRecordedEvents();
    $eventStore = new InMemoryEventStore();

    $transaction = $eventStore->commit(...iterator_to_array($events));

    expect(value: $transaction)->toBeInstanceOf(class: Transactional::class);
    expect(value: $transaction->eventStream())->toBeInstanceOf(class: DomainEvents::class);
    expect(value: $post->pullDomainEvents())->toEqual(expected: $transaction->committedEvents());
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
    $posts->saveAggregateRoot(aggregate: $post);
    $reconstitutedPost = $posts->loadAggregateRoot(aggregateId: $postId);

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
    $posts->saveAggregateRoot(aggregate: $post);
    $reconstitutedPost = $posts->loadAggregateRoot(aggregateId: $postId);

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

it('should track changes of aggregate but return the same instance.', function () {
    $postId = new PostId(value: 'c81e5118-8c94-4ba1-86e0-1ca983d96155');
    $post = Post::createPostWithoutTap(
        postId: $postId,
        title: new Title(value: 'Tracking Title Change'),
        content: new Content(value: 'Another short form content.')
    );

    $repository = new PostRepository(eventStore: new InMemoryEventStore(), projection: new InMemoryPostProjection());
    $repository->saveAggregateRoot(aggregate: $post);

    $fetchedPost1 = $repository->loadAggregateRoot(aggregateId: $post->aggregateId());
    $fetchedPost2 = $repository->loadAggregateRoot(aggregateId: $post->aggregateId());

    Assert::assertObjectEquals(expected: $fetchedPost1, actual: $fetchedPost2);

    $fetchedPost1->changeTitle(title: new Title(value: 'Title Was Changed'));
    Assert::assertObjectEquals(expected: $fetchedPost1, actual: $fetchedPost2);
});
