<?php
/***********************************************************************
 * *
 *  *
 *  * @copyright Copyright © 2018 Forix. All rights reserved.
 *  * See COPYING.txt for license details.
 *  * @author    thao@forixwebdesign.com
 * *
 */
namespace Forix\ProductLabel\Observer;

use Forix\ProductLabel\Model\Attribute;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Forix\ProductLabel\Helper\Data;
use Magento\Framework\Message\ManagerInterface;

/**
 * Class UpdateProductBadgeAfterProductSave
 *
 * @package Forix\ProductLabel\Observer
 */
class UpdateProductBadgeAfterProductSave implements ObserverInterface
{

    /**
     * @var Data
     */
    protected $helperData;

    /**
     * @var ManagerInterface
     */
    protected $messageManager;

    /**
     * @var Attribute
     */
    protected $labelAttribute;

    /**
     * UpdateProductBadgeAfterProductSave constructor.
     *
     * @param Data $helperData
     * @param ManagerInterface $messageManager
     * @param Attribute $labelAttribute
     */
    public function __construct(
        Data $helperData,
        ManagerInterface $messageManager,
        Attribute $labelAttribute
    ) {
        $this->helperData = $helperData;
        $this->messageManager = $messageManager;
        $this->labelAttribute = $labelAttribute;
    }

    /**
     * @param Observer $observer
     * @return void|mixed
     */
    public function execute(Observer $observer)
    {
        try {
            /** @var \Magento\Catalog\Model\Product $product */
            $product = $observer->getEvent()->getProduct();
            $ruleCollection = $this->helperData->getCollection();
            $labelAttribute = $this->labelAttribute->setProduct($product);

            foreach ($ruleCollection as $rule) {
                try {
                    $labelAttribute->setRule($rule);
                    $id = $labelAttribute->checkBadge();
                    if ($id !== null) {
                        $labelIds[] = $id;
                    }
                } catch (\Exception $e) {
                    continue;
                }
            }

            if (!empty($labelIds)) {
                $value = implode(',',$labelIds);
                $product->setData(Attribute::LABEL_ATTRIBUTE_CODE, $value);
                $product->getResource()->saveAttribute($product, Attribute::LABEL_ATTRIBUTE_CODE);
            }
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage('Something went wrong while updating badge attribute!');
        }
    }
}
