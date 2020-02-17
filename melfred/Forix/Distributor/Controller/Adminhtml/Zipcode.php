<?php


namespace Forix\Distributor\Controller\Adminhtml;

use Magento\Setup\Exception;

abstract class Zipcode extends \Magento\Backend\App\Action
{

    protected $_coreRegistry;

    const ADMIN_RESOURCE = 'Forix_Distributor::top_level';

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry
    ) {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context);
    }

    /**
     * Init page
     *
     * @param \Magento\Backend\Model\View\Result\Page $resultPage
     */
    public function initPage($resultPage)
    {
        $resultPage->setActiveMenu(self::ADMIN_RESOURCE)
            ->addBreadcrumb(__('Forix'), __('Forix'))
            ->addBreadcrumb(__('Zipcode'), __('Zipcode'));
        return $resultPage;
    }

    protected function saveLogAction(){
        try {
            $request = $this->getRequest();
            $userName = $this->_auth->getUser()->getUserName();
            $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/distributor_action.log');
            $logger = new \Zend\Log\Logger();
            $logger->addWriter($writer);
            $logger->info("----------------Begin Action Info-----------");
            $logger->info($userName);
            $logger->info($request->getActionName());
            $logger->debug($request->getParams());
            $logger->info("----------------End Action Info-----------");
        }catch (\Exception $e){
            $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/distributor_action.log');
            $logger = new \Zend\Log\Logger();
            $logger->addWriter($writer);
            $logger->info($e);
        }
    }
}
