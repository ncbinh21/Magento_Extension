<?php

namespace Forix\Guide\Block;

use Magento\Framework\View\Element\Template;

class Guide extends \Magento\Framework\View\Element\Template
{

    /**
     * @var \Forix\Guide\Model\ResourceModel\Download\CollectionFactory
     */
    protected $downloadCollection;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * Guide constructor.
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Forix\Guide\Model\ResourceModel\Download\CollectionFactory $downloadCollection
     * @param \Magento\Customer\Model\Session $customerSession
     * @param Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Forix\Guide\Model\ResourceModel\Download\CollectionFactory $downloadCollection,
        \Magento\Customer\Model\Session $customerSession,
        Template\Context $context,
        array $data = []
    ) {
        $this->downloadCollection = $downloadCollection;
        $this->customerSession = $customerSession;
        parent::__construct($context, $data);
    }

    /**
     * @return mixed|null
     */
    public function isDownLoaded()
    {
        $isDownload = false;
        $customerId = $this->customerSession->getCustomerId();
        if($customerId) {
            $isDownload = true;
        } else {
            if($this->customerSession->getIsDownload() == 1 ){
                $isDownload = true;
            }
        }
        return $isDownload;
    }

    public function customerId()
    {
        return $this->customerSession->getCustomerId();
    }
}