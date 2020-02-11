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
 * @package   mirasvit/module-event
 * @version   1.1.10
 * @copyright Copyright (C) 2017 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Event\Event\Product;

use Magento\Catalog\Model\Product;
use Mirasvit\Event\Event\Context;
use Mirasvit\Event\Event\ObservableEvent;
use Mirasvit\Event\EventData\ProductData;

class QtyEvent extends ObservableEvent
{
    const IDENTIFIER = 'qty_reduced';

    const PARAM_QTY_NEW = 'qty';
    const PARAM_QTY_OLD = 'old_qty';

    public function __construct(
        Context $context
    ) {
        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    public function getEvents()
    {
        return [
            self::IDENTIFIER => __('Product / Decreased QTY'),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getEventData()
    {
        return [
            $this->context->get(ProductData::class),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function expand($params)
    {
        $admin = $this->context->create(Product::class)->load($params[ProductData::ID]);

        $params[ProductData::IDENTIFIER] = $admin;

        return $params;
    }

    /**
     * {@inheritdoc}
     */
    public function toString($params)
    {
        $params = $this->expand($params);

        /** @var Product $product */
        $product = $params[ProductData::IDENTIFIER];

        return __('QTY decreased for product %1 from %2 to %3 items',
            $product->getSku(),
            $params[self::PARAM_QTY_OLD],
            $params[self::PARAM_QTY_NEW]
        );
    }

    /**
     * Register an event if product QTY has been reduced.
     *
     * @param Product $subject
     * @param mixed   $result
     *
     * @return mixed $result
     */
    public function afterSave(Product $subject, $result)
    {
        $qty = $subject->getData('quantity_and_stock_status/qty') !== null
            ? $subject->getData('quantity_and_stock_status/qty')
            : $subject->getData('stock_data/qty');
        $oldQtyArr = $subject->getOrigData('quantity_and_stock_status');
        $oldQty = is_array($oldQtyArr) && isset($oldQtyArr['qty']) ? $oldQtyArr['qty'] : false;

        if ($qty !== null && $oldQty !== false && $oldQty > $qty) {
            $params = [
                ProductData::ID          => $subject->getId(),
                self::PARAM_QTY_NEW      => $qty,
                self::PARAM_QTY_OLD      => $oldQty,
                self::PARAM_EXPIRE_AFTER => 1
            ];

            $this->context->eventRepository->register(
                self::IDENTIFIER,
                [$params[ProductData::ID]],
                $params
            );
        }

        return $result;
    }
}
