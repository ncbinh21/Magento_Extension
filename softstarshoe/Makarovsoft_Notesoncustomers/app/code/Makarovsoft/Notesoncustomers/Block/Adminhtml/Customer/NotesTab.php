<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Makarovsoft\Notesoncustomers\Block\Adminhtml\Customer;

use Magento\Backend\Block\Template\Context;
use Magento\Customer\Controller\RegistryConstants;
use Magento\Framework\Registry;
use Magento\Ui\Component\Layout\Tabs\TabWrapper;

/**
 * Class CustomerOrdersTab
 *
 * @package Magento\Sales\Block\Adminhtml
 */
class NotesTab extends TabWrapper
{
    /**
     * Core registry
     *
     * @var Registry
     */
    protected $coreRegistry = null;

    /**
     * @var bool
     */
    protected $isAjaxLoaded = true;

    /**
     * @var \Makarovsoft\Notesoncustomers\Model\NotesFactory
     */
    protected $notesFactory;

    /**
     * Constructor
     *
     * @param Context $context
     * @param Registry $registry
     * @param array $data
     */
    public function __construct(Context $context,
                                Registry $registry,
                                \Makarovsoft\Notesoncustomers\Model\NotesFactory $notesFactory,
                                array $data = [])
    {
        $this->coreRegistry = $registry;
        $this->notesFactory = $notesFactory;
        parent::__construct($context, $data);
    }

    /**
     * @inheritdoc
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Return Tab label
     *
     * @codeCoverageIgnore
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        $cnt = $this->notesFactory->create()->getNotesCount($this->coreRegistry->registry(RegistryConstants::CURRENT_CUSTOMER_ID));
        return __('Notes On Customer (%1)', $cnt);
    }

    /**
     * Return URL link to Tab content
     *
     * @return string
     */
    public function getTabUrl()
    {
        return $this->getUrl('makarovsoft_notesoncustomers/notes/view', ['_current' => true]);
    }
}
