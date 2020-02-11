<?php

namespace Forix\Checkout\Model;


use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Cms\Block\Widget\Block;
use Magento\Cms\Api\BlockRepositoryInterface;

class CheckoutConfigHelp implements ConfigProviderInterface
{
    protected $cmsBlockWidgetHelp;

    public function __construct(BlockRepositoryInterface $blockRepository, Block $block, $identifierHelp)
    {
        try {
            $this->cmsBlockWidgetHelp = $blockRepository->getById($identifierHelp)->getContent();
        } catch (\Exception $exception) {
            $this->cmsBlockWidgetHelp = '';
        }
    }

    public function getConfig()
    {
        return [
            'cmsBlockHelpHtml' => $this->cmsBlockWidgetHelp
        ];
    }
}