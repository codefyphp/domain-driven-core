<?php

/**
 * CodefyPHP
 *
 * @link       https://github.com/codefyphp/codefy
 * @copyright  2022
 * @author     Joshua Parker <josh@joshuaparker.blog>
 * @license    https://opensource.org/licenses/mit-license.php MIT License
 *
 * @since      0.1.0
 */

declare(strict_types=1);

use Codefy\Domain\Aggregate\AggregateRepository;
use Codefy\Domain\Aggregate\EventSourcedAggregateRepository;
use Codefy\Domain\EventSourcing\CorruptEventStreamException;
use Codefy\Domain\EventSourcing\EventStream;
use Codefy\Domain\EventSourcing\EventStreamIsEmptyException;
use Codefy\Domain\EventSourcing\InMemoryEventStore;
use Codefy\Tests\Content;
use Codefy\Tests\InMemoryPostProjection;
use Codefy\Tests\Post;
use Codefy\Tests\PostId;
use Codefy\Tests\Title;
use PHPUnit\Framework\Assert;

$repository = new EventSourcedAggregateRepository(
    eventStore: new InMemoryEventStore(),
    projection: new InMemoryPostProjection()
);

it('should be an instance of AggregateRepository.', function () use ($repository) {
    Assert::assertInstanceOf(expected: AggregateRepository::class, actual: $repository);
});

it('should add a new aggregate.', function () use ($repository) {
    $post = Post::createPostWithoutTap(
        postId: new PostId('cc6cc19e-bc5a-41fc-86eb-d4343eef0529'),
        title: new Title(value: 'Aggregate Post Title'),
        content: new Content(value: 'Short form content.')
    );

    $repository->saveAggregateRoot(aggregate: $post);

    $fetchedPost = $repository->loadAggregateRoot(aggregateId: $post->aggregateId());

    Assert::assertInstanceOf(expected: Post::class, actual: $fetchedPost);
    Assert::assertNotSame(expected: $post, actual: $fetchedPost);
    Assert::assertEquals(expected: 'Aggregate Post Title', actual: $fetchedPost->title());
});

it('should ignore identity map if disabled.', function () use ($repository) {
    $repository->enableIdentityMap(bool: false);

    $post = Post::createPostWithoutTap(
        postId: new PostId(value: 'cc6cc19e-bc5a-41fc-86eb-d4343eef0529'),
        title: new Title(value: 'Aggregate Post Title'),
        content: new Content(value: 'Short form content.')
    );

    $repository->saveAggregateRoot(aggregate: $post);

    $fetchedPost1 = $repository->loadAggregateRoot(aggregateId: $post->aggregateId());
    $fetchedPost2 = $repository->loadAggregateRoot(aggregateId: $post->aggregateId());

    Assert::assertNotSame(expected: $fetchedPost1, actual: $fetchedPost2);
});

it('should not intefere with other aggregates in pending events index.', function () use ($repository) {
    $post1 = Post::createPostWithoutTap(
        postId: new PostId(value: '5d9c6b3e-e861-436b-ae69-a58997fa4ef2'),
        title: new Title(value: 'First Post Event'),
        content: new Content(value: 'First short form content.')
    );

    $repository->saveAggregateRoot(aggregate: $post1);

    $post2 = Post::createPostWithoutTap(
        postId: new PostId(value: '7c438c07-07f0-4aac-9a6b-49dae2e5221a'),
        title: new Title(value: 'Second Post Event'),
        content: new Content(value: 'Second short form content.')
    );

    $repository->saveAggregateRoot(aggregate: $post2);

    $fetchedPost1 = $repository->loadAggregateRoot(aggregateId: $post1->aggregateId());
    $fetchedPost2 = $repository->loadAggregateRoot(aggregateId: $post2->aggregateId());

    $fetchedPost1->changeTitle(title: new Title(value: 'Changed Title For Post One'));
    $fetchedPost2->changeTitle(title: new Title(value: 'Changed Title For Post Two'));

    Assert::assertEquals(expected: 'Changed Title For Post One', actual: $fetchedPost1->title()->__toString());
    Assert::assertEquals(expected: 'Changed Title For Post Two', actual: $fetchedPost2->title()->__toString());
});

it('should remove aggregate from identity map when saved.', function () use ($repository) {
    $post = Post::createPostWithoutTap(
        postId: new PostId(value: 'bd38039e-4740-4453-88eb-395fbe8db528'),
        title: new Title(value: 'Aggregate Post Title'),
        content: new Content(value: 'Short form content.')
    );

    $repository->saveAggregateRoot(aggregate: $post);

    $fetchedPost1 = $repository->loadAggregateRoot(aggregateId: $post->aggregateId());

    Assert::assertNotSame(expected: $post, actual: $fetchedPost1);

    $fetchedPost2 = $repository->loadAggregateRoot(aggregateId: $post->aggregateId());

    Assert::assertNotSame(expected: $fetchedPost1, actual: $fetchedPost2);

    $fetchedPost1->changeTitle(title: new Title(value: 'Assert Equals'));

    $repository->saveAggregateRoot(aggregate: $fetchedPost1);

    $fetchedPost2 = $repository->loadAggregateRoot(aggregateId: $post->aggregateId());

    Assert::assertNotSame(expected: $fetchedPost1, actual: $fetchedPost2);
    Assert::assertEquals(expected: 'Assert Equals', actual: $fetchedPost2->title()->__toString());
});

it('should throw EventStreamIsEmptyException when aggregate root cannot be found.', function () use ($repository) {
    $post = Post::createPostWithoutTap(
        postId: new PostId(value: 'bd38039e-4740-4453-88eb-395fbe8db528'),
        title: new Title(value: 'Aggregate Post Title'),
        content: new Content(value: 'Short form content.')
    );

    $repository->saveAggregateRoot(aggregate: $post);

    $repository->loadAggregateRoot(aggregateId: new PostId(value: '1df1afff-ed79-4193-b1df-6f8c81a1d45d'));
})->throws(exception: EventStreamIsEmptyException::class);

it('should throw CorruptEventStreamException when an AggregateId does not match.', function () {
    $aggregateId = new PostId();

    $post = Post::createPostWithoutTap(
        postId: new PostId(value: 'bd38039e-4740-4453-88eb-395fbe8db528'),
        title: new Title(value: 'Aggregate Post Title'),
        content: new Content(value: 'Short form content.')
    );

    return new EventStream(
        aggregateId: $aggregateId,
        events: $post->pullDomainEvents()
    );
})->throws(exception: CorruptEventStreamException::class);
