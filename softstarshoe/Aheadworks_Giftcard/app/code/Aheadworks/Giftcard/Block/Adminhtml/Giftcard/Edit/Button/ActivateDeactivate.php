<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Giftcard\Block\Adminhtml\Giftcard\Edit\Button;

use Aheadworks\Giftcard\Api\Data\GiftcardInterface;
use Aheadworks\Giftcard\Model\GiftcardRepository;
use Aheadworks\Giftcard\Model\Source\Giftcard\Status;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Magento\Backend\Block\Widget\Context;

/**
 * Class ActivateDeactivate
 *
 * @package Aheadworks\Giftcard\Block\Adminhtml\Giftcard\Edit\Button
 */
class ActivateDeactivate implements ButtonProviderInterface
{
    /**
     * @var Context
     */
    private $context;

    /**
     * @var GiftcardRepository
     */
    private $giftcardRepository;

    /**
     * @param Context $context
     * @param GiftcardRepository $giftcardRepository
     */
    public function __construct(
        Context $context,
        GiftcardRepository $giftcardRepository
    ) {
        $this->context = $context;
        $this->giftcardRepository = $giftcardRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function getButtonData()
    {
        $data = [];
        /** @var $giftcard GiftcardInterface */
        if ($giftcard = $this->getGiftcard()) {
            if ($giftcard->getState() == Status::DEACTIVATED) {
                $data = [
                    'label'      => __('Activate'),
                    'class'      => 'activate',
                    'on_click'   => sprintf(
                        "location.href = '%s';",
                        $this->getUrl('*/*/activate', ['id' => $giftcard->getId()])
                    )
                ];
            } else {
                $data = [
                    'label'      => __('Deactivate'),
                    'class'      => 'deactivate',
                    'on_click'   => sprintf(
                        "location.href = '%s';",
                        $this->getUrl('*/*/deactivate', ['id' => $giftcard->getId()])
                    )
                ];
            }
            $data['sort_order'] = 30;
        }
        return $data;
    }

    /**
     * Retrieve gift card
     *
     * @return int|null
     */
    public function getGiftcard()
    {
        $id = $this->context->getRequest()->getParam('id');
        if ($id && $this->giftcardRepository->get($id)) {
            return $this->giftcardRepository->get($id);
        }
        return null;
    }

    /**
     * Generate url by route and parameters
     *
     * @param string $route
     * @param array $params
     * @return string
     */
    public function getUrl($route = '', $params = [])
    {
        return $this->context->getUrlBuilder()->getUrl($route, $params);
    }
}
