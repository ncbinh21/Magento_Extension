<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Fpc
 */


namespace Amasty\Fpc\Block;

use Amasty\Fpc\Model\Config;
use Amasty\Fpc\Model\PageStatus;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Layout\Element;

class Status extends Template
{
    const BLOCK_PLACEHOLDER = '<!-- amasty-fpc-status -->';

    protected $_template = 'Amasty_Fpc::status.phtml';

    /**
     * @var Config
     */
    private $config;
    /**
     * @var PageStatus
     */
    private $pageStatus;

    public function __construct(
        Template\Context $context,
        Config $config,
        PageStatus $pageStatus,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->config = $config;
        $this->pageStatus = $pageStatus;
    }

    public function getNotCacheableBlocks()
    {
        if (!$this->hasData('not_cacheable_blocks')) {
            $result = [];

            $nodes = $this->_layout->getXpath('//' . Element::TYPE_BLOCK . '[@cacheable="false"]');

            if ($nodes) {
                /** @var Element $node */
                foreach ($nodes as $node) {
                    $result [] = [
                        'name' => $node->getAttribute('name'),
                        'class' => $node->getAttribute('class'),
                    ];
                }
            }

            if ($result) {
                $this->pageStatus->setStatus(PageStatus::STATUS_IGNORED);
            }

            $this->setData('not_cacheable_blocks', $result);
        }

        return $this->getData('not_cacheable_blocks');
    }

    public function getStatusCode()
    {
        if ($this->getData('status')) {
            return $this->getData('status');
        }

        if ($this->getNotCacheableBlocks()) {
            return PageStatus::STATUS_IGNORED;
        }

        return PageStatus::STATUS_UNDEFINED;
    }

    public function getStatusTitle()
    {
        $titles = [
            PageStatus::STATUS_UNDEFINED => __('Undefined'),
            PageStatus::STATUS_HIT       => __('Cache Hit'),
            PageStatus::STATUS_MISS      => __('Cache Miss'),
            PageStatus::STATUS_IGNORED   => __('Ignored'),
        ];

        $statusCode = $this->getStatusCode();

        if (isset($titles[$statusCode])) {
            return $titles[$statusCode];
        } else {
            return $titles[PageStatus::STATUS_UNDEFINED];
        }
    }

    protected function _toHtml()
    {
        if ($this->isStatusUndefined() || !$this->config->canDisplayStatus()) {
            return self::BLOCK_PLACEHOLDER;
        } else {
            return parent::_toHtml();
        }
    }

    protected function isStatusUndefined()
    {
        return !$this->hasData('status') && !$this->getNotCacheableBlocks();
    }
}
