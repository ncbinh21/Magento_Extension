<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 30/05/2018
 * Time: 15:18
 */

namespace Forix\ProductWizard\Controller\Index;

use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use \Magento\Framework\Exception\NotFoundException;

class Index extends \Magento\Framework\App\Action\Action
{
    protected $_wizardFactory;
    protected $_registry;
    protected $_resourceWizard;

    public function __construct(
        Context $context,
        \Magento\Framework\Registry $registry,
        \Forix\ProductWizard\Model\ResourceModel\Wizard $resourceWizard,
        \Forix\ProductWizard\Model\WizardFactory $wizardFactory
    )
    {
        $this->_registry = $registry;
        $this->_wizardFactory = $wizardFactory;
        $this->_resourceWizard = $resourceWizard;
        parent::__construct($context);
    }

    /**
     * @param $wizardId
     * @return \Forix\ProductWizard\Model\Wizard|null
     */
    protected function _iniWizard($wizardId)
    {
        if (is_numeric($wizardId)) {
            $wizard = $this->_wizardFactory->create();
            $this->_resourceWizard->load($wizard, $wizardId, 'wizard_id');
            if ($wizard->getId()) {
                $this->_registry->register('current_wizard', $wizard, true);
                return $wizard;
            }
        }
        return null;
    }

    public function execute()
    {
        $wizardId = $this->getRequest()->getParam('wizard_id');
        if ($wizardId) {
            if ($wizard = $this->_iniWizard($wizardId)) {
                $this->_view->getLayout()->getUpdate()->addHandle($wizard->getTemplateUpdate());
                return $this->resultFactory->create(ResultFactory::TYPE_PAGE);
            }
        }
        throw new NotFoundException(__('Parameter is incorrect.'));
    }
}