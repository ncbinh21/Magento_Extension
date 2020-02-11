<?php

namespace Forix\Checkout\Model;


use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Cms\Block\Widget\Block;
use Magento\Cms\Api\BlockRepositoryInterface;

class CheckoutConfigOffPayment implements ConfigProviderInterface
{
    protected $cmsBlockWidgetOffPayment;

    public function __construct(BlockRepositoryInterface $blockRepository, Block $block, $identifierOffPayment)
    {
        try {
            $this->cmsBlockWidgetOffPayment = $blockRepository->getById($identifierOffPayment)->getContent();
        } catch (\Exception $exception) {
            $this->cmsBlockWidgetOffPayment = '';
        }
    }

    public function getConfig()
    {
        return [
            'cmsOffPaymentHtml' => $this->cmsBlockWidgetOffPayment
        ];
    }
}