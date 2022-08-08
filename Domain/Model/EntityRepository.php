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

namespace Codefy\Domain\Model;

interface EntityRepository
{
    /**
     * Find an Entity by its id.
     *
     * @param EntityId $entityId Entity id.
     * @return Entity|null Returns Entity object if existed, null otherwise.
     * @throws EntityNotFoundException The ID of this entity is invalid.
     */
    public function findEntityById(EntityId $entityId): ?Entity;

    /**
     * Save an Entity.
     *
     * @return EntityId|null If saved successfully, return EntityId, null otherwise.
     */
    public function saveEntity(Entity $entity): ?EntityId;
}
