<?php

namespace Forix\Checkout\Model;


use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Cms\Block\Widget\Block;
use Magento\Cms\Api\BlockRepositoryInterface;

class CheckoutConfigInfo implements ConfigProviderInterface
{
    protected $cmsBlockWidgetInfo;

    public function __construct(BlockRepositoryInterface $blockRepository, Block $block, $identifierInfo)
    {
        try {
            $this->cmsBlockWidgetInfo = $blockRepository->getById($identifierInfo)->getContent();
        } catch (\Exception $exception) {
            $this->cmsBlockWidgetInfo = '';
        }
    }

    public function getConfig()
    {
        return [
            'cmsBlockInfoHtml' => $this->cmsBlockWidgetInfo
        ];
    }
}