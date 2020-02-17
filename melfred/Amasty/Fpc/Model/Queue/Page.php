<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Fpc
 */


namespace Amasty\Fpc\Model\Queue;

use Amasty\Fpc\Api\Data\QueuePageInterface;
use Magento\Framework\Model\AbstractModel;

class Page extends AbstractModel implements QueuePageInterface
{
    /**
     * @var PageFactory
     */
    private $pageFactory;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Amasty\Fpc\Model\Queue\PageFactory $pageFactory,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->pageFactory = $pageFactory;
    }

    protected function _construct()
    {
        $this->_init('Amasty\Fpc\Model\ResourceModel\Queue\Page');
    }

    /**
     * {@inheritdoc}
     */
    public function getUrl()
    {
        return $this->_getData(QueuePageInterface::URL);
    }

    /**
     * {@inheritdoc}
     */
    public function setUrl($url)
    {
        $this->setData(QueuePageInterface::URL, $url);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getRate()
    {
        return $this->_getData(QueuePageInterface::RATE);
    }

    /**
     * {@inheritdoc}
     */
    public function setRate($rate)
    {
        $this->setData(QueuePageInterface::RATE, $rate);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getStore()
    {
        return $this->_getData(QueuePageInterface::STORE);
    }

    /**
     * {@inheritdoc}
     */
    public function setStore($store)
    {
        $this->setData(QueuePageInterface::STORE, $store);

        return $this;
    }
}
