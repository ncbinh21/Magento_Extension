<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Giftcard\Model\ResourceModel\Validator;

use Aheadworks\Giftcard\Api\Data\GiftcardInterface;
use Aheadworks\Giftcard\Api\Data\Pool\CodeInterface as PoolCodeInterface;
use Aheadworks\Giftcard\Model\Source\YesNo;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\EntityManager\MetadataPool;

/**
 * Class GiftcardIsUnique
 *
 * @package Aheadworks\Giftcard\Model\ResourceModel\Validator
 */
class GiftcardIsUnique
{
    /**
     * @var MetadataPool
     */
    private $metadataPool;

    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @param MetadataPool $metadataPool
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        MetadataPool $metadataPool,
        ResourceConnection $resourceConnection
    ) {
        $this->metadataPool = $metadataPool;
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * Check unique Gift Card code
     *
     * @param string $code
     * @return bool
     */
    public function validate($code)
    {
        $giftcardMetaData = $this->metadataPool->getMetadata(GiftcardInterface::class);
        $connection = $this->resourceConnection->getConnectionByName($giftcardMetaData->getEntityConnectionName());

        $bind = ['code' => $code];
        $select = $connection->select()
            ->from($this->resourceConnection->getTableName($giftcardMetaData->getEntityTable()))
            ->where('code = :code');
        if ($connection->fetchRow($select, $bind)) {
            return false;
        }

        $bind = [
            'code' => $code,
            'used' => YesNo::NO
        ];
        $poolCodeMetaData = $this->metadataPool->getMetadata(PoolCodeInterface::class);
        $connection = $this->resourceConnection->getConnectionByName($poolCodeMetaData->getEntityConnectionName());
        $select = $connection->select()
            ->from($this->resourceConnection->getTableName($poolCodeMetaData->getEntityTable()))
            ->where('code = :code')
            ->where('used = :used');
        if ($connection->fetchRow($select, $bind)) {
            return false;
        }
        return true;
    }
}
