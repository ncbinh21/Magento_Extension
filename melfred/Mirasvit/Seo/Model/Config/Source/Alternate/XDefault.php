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
 * @package   mirasvit/module-seo
 * @version   2.0.64
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Seo\Model\Config\Source\Alternate;

use Magento\Framework\Option\ArrayInterface;
use Mirasvit\Seo\Api\Config\AlternateConfigInterface as AlternateConfig;

class XDefault implements ArrayInterface
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;

     /**
      * @param \Magento\Framework\App\State $state
      * @param \Magento\Store\Model\StoreManagerInterface $storeManager
      * @param \Magento\Framework\App\Request\Http $request
      */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Request\Http $request
    ) {
        $this->storeManager = $storeManager;
        $this->request = $request;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options = [
            ['value' => 0, 'label' => __('Disabled')],
            ['value' => AlternateConfig::X_DEFAULT_AUTOMATICALLY, 'label' => __('Add Automatically')],
        ];

        $storeIds = $this->storeManager->getWebsite($this->request->getParam('website'))->getStoreIds();

        foreach ($storeIds as $storeId) {
            $store = $this->storeManager->getStore($storeId);
            if ($store->isActive()) {
                $options[] = [
                    'value' => $store->getId(),
                    'label' => $store->getName() . ' — ' . $store->getBaseUrl()
                ];
            }

        }

        return $options;
    }

}
