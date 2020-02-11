<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Giftcard\Ui\Component\Listing\Column\Giftcard;

use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Class Order
 *
 * @package Aheadworks\Giftcard\Ui\Component\Listing\Column
 */
class Order extends Column
{
    /**
     * {@inheritdoc}
     */
    public function prepareDataSource(array $dataSource)
    {
        $fieldName = $this->getData('name');
        foreach ($dataSource['data']['items'] as &$item) {
            if ($orderId = $item['order_id']) {
                if ($orderIncrementId = $item[$fieldName]) {
                    $item[$fieldName . '_url'] = $this->context->getUrl('sales/order/view', ['order_id' => $orderId]);
                    $item[$fieldName . '_label'] = $orderIncrementId;
                } else {
                    $item[$fieldName . '_label'] = $orderId;
                }
            }
        }
        return $dataSource;
    }
}
