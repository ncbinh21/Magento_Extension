<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Giftcard\Controller\Adminhtml\Giftcard;

use Aheadworks\Giftcard\Model\Source\Giftcard\Status;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class MassDeactivate
 *
 * @package Aheadworks\Giftcard\Controller\Adminhtml\Giftcard
 */
class MassDeactivate extends MassAbstract
{
    /**
     * {@inheritdoc}
     */
    protected function massAction($collection)
    {
        $count = 0;
        foreach ($collection->getItems() as $item) {
            try {
                $giftcardCode = $this->giftcardRepository->get($item->getId());
                $giftcardCode->setState(Status::DEACTIVATED);
                $this->giftcardRepository->save($giftcardCode);
                $count++;
            } catch (LocalizedException $e) {
            }
        }
        $this->messageManager->addSuccessMessage(__('A total of %1 Gift Card code(s) have been deactivated', $count));
    }
}
