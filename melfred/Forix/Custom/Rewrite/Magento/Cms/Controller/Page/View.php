<?php

namespace Forix\Custom\Rewrite\Magento\Cms\Controller\Page;

class View extends \Magento\Cms\Controller\Page\View
{
    /**
     * View CMS page action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $pageId = $this->getRequest()->getParam('page_id', $this->getRequest()->getParam('id', false));
        $resultPage = $this->_objectManager->get(\Magento\Cms\Helper\Page::class)->prepareResultPage($this, $pageId);
        if (!$resultPage) {
            $resultForward = $this->resultForwardFactory->create();
            return $resultForward->forward('noroute');
        }
        if($pageId ==  52 || $pageId == 48) {
            $resultPage->getConfig()->setRobots('NOINDEX,NOFOLLOW');
        }
        return $resultPage;
    }
}
