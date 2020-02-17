<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Fpc
 */


namespace Amasty\Fpc\Observer;

use Amasty\Fpc\Block\Status as StatusBlock;
use Amasty\Fpc\Model\Config;
use Amasty\Fpc\Model\PageStatus;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\View\Element\BlockFactory;

class SendResponseBefore implements ObserverInterface
{
    /**
     * @var Config
     */
    private $config;
    /**
     * @var BlockFactory
     */
    private $blockFactory;
    /**
     * @var PageStatus
     */
    private $pageStatus;

    public function __construct(
        Config $config,
        BlockFactory $blockFactory,
        PageStatus $pageStatus
    ) {
        $this->config = $config;
        $this->blockFactory = $blockFactory;
        $this->pageStatus = $pageStatus;
    }

    public function execute(Observer $observer)
    {
        /** @var \Magento\Framework\App\Request\Http $request */
        $request = $observer->getData('request');

        if ($request->isAjax() || !$request->isGet()) {
            return;
        }

        /** @var ResponseInterface $response */
        $response = $observer->getData('response');

        if (!$response instanceof \Magento\Framework\App\Response\Http) {
            return;
        }

        if (!$this->config->canDisplayStatus()) {
            return;
        }

        $status = $this->pageStatus->getStatus();

        if ($status == PageStatus::STATUS_IGNORED) { // Block already rendered
            return;
        }

        $body = $response->getBody();

        /** @var StatusBlock $block */
        $block = $this->blockFactory->createBlock('\Amasty\Fpc\Block\Status');
        $block->setData('status', $status);
        $html = $block->toHtml();

        $body = str_replace(StatusBlock::BLOCK_PLACEHOLDER, $html, $body);

        $response->setBody($body);
    }
}
