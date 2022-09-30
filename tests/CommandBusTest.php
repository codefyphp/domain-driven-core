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

use Codefy\CommandBus\Busses\SynchronousCommandBus;
use Codefy\CommandBus\Command;
use Codefy\CommandBus\Containers\ContainerFactory;
use Codefy\CommandBus\Containers\Psr11Container;
use Codefy\CommandBus\Exceptions\CommandCouldNotBeHandledException;
use Codefy\CommandBus\Exceptions\CommandPropertyNotFoundException;
use Codefy\CommandBus\InvalidPayloadException;
use Codefy\CommandBus\Odin;
use Codefy\CommandBus\PropertyCommand;
use Codefy\CommandBus\Resolvers\NativeCommandHandlerResolver;
use Codefy\Tests\CreatePostCommand;
use Codefy\Tests\CreatePostCommandHandler;
use Codefy\Tests\SelfHandlingCommand;
use Codefy\Tests\PostId;
use Qubus\Exception\Data\TypeException;
use Qubus\Exception\Http\Client\NotFoundException;
use Qubus\Injector\Config\InjectorConfig;
use Qubus\Injector\Psr11\Container;

$config = include 'commandbus.php';

try {
    $postId = PostId::fromNative('760b7c16-b28e-4d31-9f93-7a2f0d3a1c51');
} catch (TypeException $e) {
}

it('should validate command with required and allowed fields.', function () {
    $payload = CreatePostCommand::validate(
        payload: [
            'postId' => 'required',
            'postTitle' => 'required',
            'postContent' => 'allowed',
            'postAuthor' => 'ignored',
        ]
    );

    $valid = [
        'postId' => 'required',
        'postTitle' => 'required',
        'postContent' => 'allowed',
    ];

    expect(value: $payload)->toEqual(expected: $valid);
});

it('should throw InvalidPayloadException when required fields are missing.', function () {
    CreatePostCommand::validate(
        payload: [
            'postContent' => 'allowed',
            'postAuthor' => 'ignored',
        ]
    );
})->throws(exception: InvalidPayloadException::class);

it('should return same values from payload.', function () use ($postId) {
    $command = CreatePostCommand::fromPayload(
        payload: [
            'postId' => $postId->__toString(),
            'postTitle' => 'New Post Title',
            'postContent' => 'Short form content.',
        ]
    );

    expect(value: $command->postId())->toEqual(expected: $postId->__toString())
        ->and(value: $command->title())->toEqual(expected: 'New Post Title')
        ->and(value: $command->content())->toEqual(expected: 'Short form content.');
});

it('should fire by auto resolution.', function () use ($postId, $config) {
    $resolver = new NativeCommandHandlerResolver(container: ContainerFactory::make($config['container']));

    $odin = new Odin(new SynchronousCommandBus($resolver));
    $command = CreatePostCommand::fromPayload(
        payload: [
            'postId' => $postId->__toString(),
            'postTitle' => 'New Post Title',
            'postContent' => 'Short form content.',
        ]
    );
    $odin->execute(command: $command);

    expect(value: $command->postId())->toEqual(expected: $postId->__toString())
        ->and(value: $command->title())->toEqual(expected: 'New Post Title')
        ->and(value: $command->content())->toEqual(expected: 'Short form content.');
});

it('should fire the handler attached by callable.', function () use ($config) {
    $resolver = new NativeCommandHandlerResolver(container: ContainerFactory::make(config: $config['container']));
    $resolver->bindHandler(commandName: CreatePostCommand::class, handler: function (Command $command) {
        $command->postTitle = 'Second Post Title';
    });
    $odin = new Odin(bus: new SynchronousCommandBus(resolver: $resolver));
    $command = new CreatePostCommand();
    $odin->execute(command: $command);

    expect(value: $command->postTitle)->toEqual(expected: 'Second Post Title');
});

it('should fire the handler attached by string.', function () use ($postId, $config) {
    $resolver = new NativeCommandHandlerResolver(container: ContainerFactory::make(config: $config['container']));
    $resolver->bindHandler(
        commandName: CreatePostCommand::class,
        handler: CreatePostCommandHandler::class
    );

    $odin = new Odin(bus: new SynchronousCommandBus(resolver: $resolver));
    $command = CreatePostCommand::fromPayload(
        payload: [
            'postId' => $postId->__toString(),
            'postTitle' => 'Third Post Title',
            'postContent' => 'Short form content.',
        ]
    );
    $odin->execute(command: $command);

    expect(value: $command->postId())->toEqual(expected: $postId->__toString())
        ->and(value: $command->title())->toEqual(expected: 'Third Post Title')
        ->and(value: $command->content())->toEqual(expected: 'Short form content.');
});

it('should handle itself when implementing CommandHandler.', function () {
    $productId = '7a0e6c16-c1ed-4fcf-b498-cdce9fa763d2';

    $odin = new Odin();
    $command = new SelfHandlingCommand(
        payload: [
            'productId' => $productId,
            'productName' => 'Macbook Pro 2016',
        ]
    );
    $odin->execute(command: $command);

    expect(value: $command->productName())->toEqual(expected: 'Macbook Pro 2016');
});

it('should throw an implementation of NotFoundExceptionInterface.', function () {
    $exception = (new Psr11Container(new Container(new InjectorConfig([]))))->make(class: 'User');

    expect(value: 'No entry found: User')->toEqual(expected: $exception->getMessage());
})->throws(exception: NotFoundException::class);

it('should return defined property.', function () {
    $testCommand = new class () extends PropertyCommand {
        public PostId $postId;
    };

    $command = new $testCommand(['postId' => new PostId(value: '5161d369-c566-4379-9f25-a7d4f8bd1661')]);

    expect(value: new PostId('5161d369-c566-4379-9f25-a7d4f8bd1661'))->toEqual(expected: $command->postId);
});

it('should throw a CommandPropertyNotFoundException when property is not defined in command class.', function () {
    $testCommand = new class () extends PropertyCommand {
        public PostId $postId;
    };

    $command = new $testCommand(['title' => 'Command Property Not Defined']);
})->throws(exception: CommandPropertyNotFoundException::class);

it('should throw a CommandCouldNotBeHandledException.', function () use ($config) {
    $testCommand = new class () extends PropertyCommand {
        public string $title;
    };

    $testCommandHandler = new class () {
    };

    $resolver = new NativeCommandHandlerResolver(container: ContainerFactory::make(config: $config['container']));
    $resolver->bindHandler(
        commandName: $testCommand::class,
        handler: $testCommandHandler::class
    );

    $odin = new Odin(bus: new SynchronousCommandBus(resolver: $resolver));
    $command = new $testCommand(['title' => 'Handle Method Could Not Be Found']);

    $odin->execute(command: $command);
})->throws(exception: CommandCouldNotBeHandledException::class);
