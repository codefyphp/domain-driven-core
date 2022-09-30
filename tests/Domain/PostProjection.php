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

namespace Codefy\Tests;

use Codefy\Domain\EventSourcing\Projection;

interface PostProjection extends Projection
{
    /**
     * Projects when post was created.
     *
     * Used for testing purposes, but in production, this method
     * should return void.
     *
     * @param PostWasCreated $event
     * @return array
     */
    public function projectWhenPostWasCreated(PostWasCreated $event): array;

    /**
     * Projects when title was changed.
     *
     * Used for testing purposes, but in production, this method
     * should return void.
     *
     * @param TitleWasChanged $event
     * @return array
     */
    public function projectWhenTitleWasChanged(TitleWasChanged $event): array;
}
