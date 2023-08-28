<?php

/**
 * CodefyPHP
 *
 * @link       https://github.com/codefyphp/domain-driven-core
 * @copyright  2022
 * @author     Joshua Parker <joshua@joshuaparker.dev
 * @license    https://opensource.org/licenses/mit-license.php MIT License
 */

declare(strict_types=1);

namespace Codefy\CommandBus;

use Codefy\CommandBus\Exceptions\CommandPropertyNotFoundException;
use ReflectionClass;

use function property_exists;
use function sprintf;

/**
 * Abstract class for constructing property mappings.
 *
 * Example:
 *          final class CreatePostCommand extends PropertyCommand
 *          {
 *              public PostId $postId;
 *          }
 *
 *          $command = new CreatePostCommand(['postId' => new PostId()]);
 *          $odin->execute($command);
 */
abstract class PropertyCommand implements Command
{
    /**
     * @throws CommandPropertyNotFoundException
     */
    public function __construct(array $data = [])
    {
        foreach ($data as $key => $value) {
            if (!property_exists($this, $key)) {
                $command = (new ReflectionClass($this))->getShortName();
                throw new CommandPropertyNotFoundException(
                    message: sprintf(
                        '$this->%s is not a valid property in %s',
                        $key,
                        $command
                    )
                );
            }
            $this->$key = $value;
        }
    }
}
