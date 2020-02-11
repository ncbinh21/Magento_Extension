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


namespace Mirasvit\Report\Model\Query\Column;

use Magento\Store\Model\StoreManagerInterface;
use Mirasvit\Report\Model\Config\Map;
use Mirasvit\Report\Model\Query\Column;
use Mirasvit\Report\Api\Repository\MapRepositoryInterface;

class Store extends Column
{
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * {@inheritdoc}
     */
    public function __construct(
        \Magento\Framework\Api\FilterBuilder $filterBuilder,
        StoreManagerInterface $storeManager,
        MapRepositoryInterface $mapRepository,
        $name,
        $data = []
    ) {
        parent::__construct($filterBuilder, $mapRepository, $name, $data);

        $this->storeManager = $storeManager;
    }

    /**
     * @return array
     */
    public function getJsConfig()
    {
        $options = [[
            'label'    => __('All Store Views'),
            'type'     => 'all',
            'storeIds' => '',
        ]];

        $websites = $this->storeManager->getWebsites();

        foreach ($websites as $website) {
            $options[] = [
                'label'    => $website->getName(),
                'type'     => 'website',
                'storeIds' => implode(',', $website->getStoreIds()),
            ];

            /** @var \Magento\Store\Model\Group $group */
            foreach ($website->getGroups() as $group) {
                $options[] = [
                    'label'    => $group->getName(),
                    'type'     => 'group',
                    'storeIds' => implode(',', $group->getStoreIds()),
                ];

                /** @var \Magento\Store\Model\Store $store */
                foreach ($group->getStores() as $store) {
                    $options[] = [
                        'label'    => $store->getName(),
                        'type'     => 'store',
                        'storeIds' => $store->getId(),
                    ];
                }
            }
        }

        return [
            'component' => 'Mirasvit_Report/js/toolbar/filter/store',
            'value'     => null,
            'current'   => __('All Store Views'),
            'stores'    => $options,
            'column'    => $this->getName(),
        ];
    }

    /**
     * @inheritDoc
     */
    public function filter(\Mirasvit\Report\Ui\DataProvider $dataProvider, $value)
    {
        $this->filterBuilder
            ->setField($this->getName())
            ->setConditionType('eq')
            ->setValue($value);

        $dataProvider->addFilter($this->filterBuilder->create());

        return $this;
    }
}
