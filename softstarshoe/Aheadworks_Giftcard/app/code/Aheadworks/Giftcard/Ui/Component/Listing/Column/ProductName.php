<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Giftcard\Ui\Component\Listing\Column;

use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Class ProductName
 *
 * @package Aheadworks\Giftcard\Ui\Component\Listing\Column
 */
class ProductName extends Column
{
    /**
     * {@inheritdoc}
     */
    public function prepareDataSource(array $dataSource)
    {
        $fieldName = $this->getData('config/fieldName');
        $awgcBackUrlParam = $this->getData('config/awgcBackUrlParam')
            ? ['awgcBack' => $this->getData('config/awgcBackUrlParam')]
            : [];
        $columnName = $this->getData('name');
        foreach ($dataSource['data']['items'] as &$item) {
            if ($productId = $item[$fieldName]) {
                if ($productName = $item[$columnName]) {
                    $item[$columnName . '_url'] = $this->context->getUrl(
                        'aw_giftcard_admin/product/edit',
                        array_merge(['id' => $productId], $awgcBackUrlParam)
                    );
                    $item[$columnName . '_label'] = $productName;
                } else {
                    $item[$columnName . '_label'] = $productId;
                }
            }
        }
        return $dataSource;
    }
}
