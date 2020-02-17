<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Fpc
 */


namespace Amasty\Fpc\Block\Adminhtml\Log\Index;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class ClearButton implements ButtonProviderInterface
{
    /**
     * Url Builder
     *
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;

    public function __construct(
        \Magento\Backend\Block\Widget\Context $context
    ) {
        $this->urlBuilder = $context->getUrlBuilder();
    }

    public function getButtonData()
    {
        $data = [
            'label' => __('Clear Log'),
            'class' => 'delete primary',
            'on_click' => 'deleteConfirm(\'' . __(
                    'Are you sure you want to do this?'
                ) . '\', \'' . $this->urlBuilder->getUrl('*/*/clear') . '\')',
            'sort_order' => 20,
        ];

        return $data;
    }
}
