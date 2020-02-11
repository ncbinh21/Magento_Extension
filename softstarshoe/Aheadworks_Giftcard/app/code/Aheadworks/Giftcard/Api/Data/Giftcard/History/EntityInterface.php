<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Giftcard\Api\Data\Giftcard\History;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface EntitesInterface
 * @api
 */
interface EntityInterface extends ExtensibleDataInterface
{
    /**#@+
     * Constants defined for keys of the data array. Identical to the name of the getter in snake case
     */
    const HISTORY_ID = 'history_id';
    const ENTITY_TYPE = 'entity_type';
    const ENTITY_ID = 'entity_id';
    const ENTITY_LABEL = 'entity_label';
    /**#@-*/

    /**
     * Get history id
     *
     * @return int
     */
    public function getHistoryId();

    /**
     * Set history id
     *
     * @param int $historyId
     * @return $this
     */
    public function setHistoryId($historyId);

    /**
     * Get entity type
     *
     * @return int
     */
    public function getEntityType();

    /**
     * Set entity type
     *
     * @param int $entityType
     * @return $this
     */
    public function setEntityType($entityType);

    /**
     * Get entity id
     *
     * @return int
     */
    public function getEntityId();

    /**
     * Set entity id
     *
     * @param int $entityId
     * @return $this
     */
    public function setEntityId($entityId);

    /**
     * Get entity label
     *
     * @return string
     */
    public function getEntityLabel();

    /**
     * Set entity label
     *
     * @param string $entityLabel
     * @return $this
     */
    public function setEntityLabel($entityLabel);

    /**
     * Retrieve existing extension attributes object or create a new one
     *
     * @return \Aheadworks\Giftcard\Api\Data\Giftcard\History\EntityExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object
     *
     * @param \Aheadworks\Giftcard\Api\Data\Giftcard\History\EntityExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Aheadworks\Giftcard\Api\Data\Giftcard\History\EntityExtensionInterface $extensionAttributes
    );
}
