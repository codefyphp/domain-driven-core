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

$config = include 'commandbus.php';

use Codefy\CommandBus\Busses\SynchronousCommandBus;
use Codefy\CommandBus\Containers\ContainerFactory;
use Codefy\CommandBus\Odin;
use Codefy\CommandBus\Resolvers\NativeCommandHandlerResolver;
use Codefy\Tests\CommandBus\CreatePostCommand;
use Codefy\Tests\CommandBus\PostEventBusCommandHandler;

$resolver = new NativeCommandHandlerResolver(container: ContainerFactory::make(config: $config['container']));

it('should fire the event bus handler attached by string.', function () use ($config, $resolver) {
    $postId = '7a0e6c16-c1ed-4fcf-b498-cdce9fa763d2';

    $resolver->bindHandler(
        commandName: CreatePostCommand::class,
        handler: PostEventBusCommandHandler::class
    );

    $odin = new Odin(bus: new SynchronousCommandBus(resolver: $resolver));
    $command = CreatePostCommand::fromPayload(
        payload: [
            'postId' => $postId,
            'postTitle' => 'Event Bus Command',
            'postContent' => 'Testing the event bus command handler.'
        ]
    );
    $odin->execute(command: $command);

    expect(value: $command->postId())->toEqual(expected: $postId)
        ->and(value: $command->title())->toEqual(expected: 'Event Bus Command')
        ->and(value: $command->content())->toEqual(expected: 'Testing the event bus command handler.');
});
