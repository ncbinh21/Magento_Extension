<?php

namespace Forix\Checkout\Model;


use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Cms\Block\Widget\Block;
use Magento\Cms\Api\BlockRepositoryInterface;

class CheckoutConfigShipping implements ConfigProviderInterface
{
    protected $cmsBlockWidgetShip;

    public function __construct(BlockRepositoryInterface $blockRepository, Block $block, $identifierShip)
    {
        try {
            $this->cmsBlockWidgetShip = $blockRepository->getById($identifierShip)->getContent();
        } catch (\Exception $exception) {
            $this->cmsBlockWidgetShip = '';
        }
    }

    public function getConfig()
    {
        return [
            'cmsBlockShippingHtml' => $this->cmsBlockWidgetShip
        ];
    }
}