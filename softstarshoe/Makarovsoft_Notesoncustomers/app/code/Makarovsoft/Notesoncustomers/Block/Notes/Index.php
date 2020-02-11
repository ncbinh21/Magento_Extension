<?php

namespace Makarovsoft\Notesoncustomers\Block\Notes;

class Index extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Framework\Stdlib\DateTime
     */
    protected $dateTime;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Foggyline\Helpdesk\Model\TicketFactory
     */
    protected $ticketFactory;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Stdlib\DateTime $dateTime,
        \Magento\Customer\Model\Session $customerSession,
        \Makarovsoft\Notesoncustomers\Model\NotesFactory $notesFactory,
        array $data = []
    )
    {
        $this->dateTime = $dateTime;
        $this->customerSession = $customerSession;
        $this->notesFactory = $notesFactory;
        parent::__construct($context, $data);
    }

    /**
     * @return \Makarovsoft\Notesoncustomers\Model\ResourceModel\Notes\Collection
     */
    public function getNotes()
    {
        $collection = $this->notesFactory->create()->getNotes($this->customerSession->getCustomerId());
        $collection->addFieldToFilter('visible', 1);
        return $collection;
    }
}
