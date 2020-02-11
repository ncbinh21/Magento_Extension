<?php
/**
 * Created by Mercurial
 * Job Title: Magento Developer
 * Project Name: M2 - SoftStart Shoes
 * Date: 09/03/2018
 */
namespace Forix\QuoteLetter\Plugin;

use Magento\Framework\App\Action\Action;

class CmsPageTemplate
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $pageFactory;

    /**
     * @var \Magento\Cms\Model\Page
     */
    protected $page;

    /**
     * CmsPageTemplate constructor.
     * @param \Magento\Framework\View\Result\PageFactory $pageFactory
     * @param \Magento\Cms\Model\Page $page
     */
    public function __construct(
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        \Magento\Cms\Model\Page $page
    ) {
        $this->pageFactory = $pageFactory;
        $this->page = $page;
    }

    /**
     * @param \Magento\Cms\Helper\Page $subject
     * @param \Closure $proceed
     * @param Action $action
     * @param null $pageId
     * @return \Magento\Framework\View\Result\Page
     */
    public function aroundPrepareResultPage(\Magento\Cms\Helper\Page $subject, \Closure $proceed, Action $action, $pageId = null)
    {
        $duplicatePageId = $pageId;

        if ($pageId !== null) {
            $delimiterPosition = strrpos($pageId, '|');
            if ($delimiterPosition) {
                $pageId = substr($pageId, 0, $delimiterPosition);
            }
        }
        if ($pageData = $this->page->load($duplicatePageId)) {
            $activeQuote = $pageData->getIsActiveQuote();
        }
        $resultPage = $this->pageFactory->create();
        if($activeQuote) {
            $resultPage->addHandle('cms_page_quote');
        } else {
            $resultPage->addHandle('cms_page_quote_add_breadcrumb');
        }

        $resultPage = $proceed($action, $duplicatePageId);
        return $resultPage;
    }
}