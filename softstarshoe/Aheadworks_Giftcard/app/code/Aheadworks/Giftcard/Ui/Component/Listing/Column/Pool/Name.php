<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Giftcard\Ui\Component\Listing\Column\Pool;

use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Class Name
 *
 * @package Aheadworks\Giftcard\Ui\Component\Listing\Column
 */
class Name extends Column
{
    /**
     * {@inheritdoc}
     */
    public function prepareDataSource(array $dataSource)
    {
        $fieldName = $this->getData('name');
        foreach ($dataSource['data']['items'] as & $item) {
            $item[$fieldName . '_label'] = $item['name'];
            $item[$fieldName . '_url'] = $this->context->getUrl(
                'aw_giftcard_admin/pool/edit',
                ['id' => $item['id']]
            );
        }

        return $dataSource;
    }
}
