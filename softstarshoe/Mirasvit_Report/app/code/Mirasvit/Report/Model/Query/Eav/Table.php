<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-report
 * @version   1.2.27
 * @copyright Copyright (C) 2017 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Report\Model\Query\Eav;

use Magento\Framework\App\CacheInterface;
use Magento\Framework\App\ResourceConnection;
use Mirasvit\Report\Api\Factory\TableDescriptorFactoryInterface;
use Mirasvit\Report\Api\Repository\MapRepositoryInterface;
use Mirasvit\Report\Model\Query\FieldFactory;
use Mirasvit\Report\Model\Query\Eav\FieldFactory as EavFieldFactory;
use Magento\Eav\Model\EntityFactory as EavEntityFactory;
use Mirasvit\Report\Model\Query\ColumnFactory;
use Magento\Eav\Model\Config;


class Table extends \Mirasvit\Report\Model\Query\Table
{
    /**
     * @var EavEntityFactory
     */
    private $eavEntityFactory;

    /**
     * @var Config
     */
    private $eavConfig;

    /**
     * @var CacheInterface
     */
    private $cache;

    /**
     * {@inheritdoc}
     */
    public function __construct(
        TableDescriptorFactoryInterface $tableDescriptorFactory,
        MapRepositoryInterface $mapRepository,
        EavEntityFactory $eavEntityFactory,
        CacheInterface $cache,
        Config $eavConfig,
        $name,
        $type,
        $connection = 'default'
    ) {
        parent::__construct($tableDescriptorFactory, $mapRepository, $name, $connection);

        $this->eavEntityFactory = $eavEntityFactory;
        $this->eavConfig = $eavConfig;
        $this->cache = $cache;

        $this->initByEntityType($type);
    }

    /**
     * @param string $type
     * @return void
     */
    protected function initByEntityType($type)
    {
        $entityTypeId = (int)$this->eavEntityFactory->create()->setType($type)->getTypeId();

        $attributeCodes = $this->eavConfig->getEntityAttributeCodes($entityTypeId);
        foreach ($attributeCodes as $attributeCode) {
            if (in_array($attributeCode, ['category_ids', 'media_gallery'])) {
                continue;
            }

            $attribute = $this->eavConfig->getAttribute($entityTypeId, $attributeCode);

            $field = $this->mapRepository->createEavField([
                'table'        => $this,
                'name'         => $attributeCode,
                'entityTypeId' => $type,
            ]);

            $this->fieldsPool[$field->getName()] = $field;

            if ($attribute->getDefaultFrontendLabel()) {
                $options = false;

                if ($attribute->usesSource()) {
                    $identifier = $attribute->getAttributeCode() . 'options';
                    $cache = $this->cache->load($identifier);
                    if ($cache) {
                        $options = \Zend_Json::decode($cache);
                    } else {
                        $options = $attribute->getSource()->toOptionArray();
                        $this->cache->save(\Zend_Json::encode($options), $identifier);

                    }
                }

                $this->mapRepository->createColumn([
                    'name' => $attributeCode,
                    'data' => [
                        'label'   => $attribute->getDefaultFrontendLabel(),
                        'type'    => $attribute->getFrontendInput(),
                        'options' => $options,
                        'table'   => $this,
                        'fields'  => [
                            $attributeCode,
                        ],
                    ],
                ]);
            }
        }

    }
}
