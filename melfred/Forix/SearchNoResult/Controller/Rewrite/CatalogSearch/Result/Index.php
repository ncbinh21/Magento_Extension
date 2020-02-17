<?php

namespace Forix\SearchNoResult\Controller\Rewrite\CatalogSearch\Result;

use Forix\SearchNoResult\Helper\Data;
use Magento\Catalog\Model\Layer\Resolver;
use Magento\Catalog\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Search\Model\QueryFactory;
use Magento\CatalogSearch\Helper\Data as SearchHelper;


class Index extends \Amasty\Shopby\Controller\Search\Result\Index
{

    protected $_layerResolver;
    protected $_queryFactory;
    protected $_helper;
    protected $searchHelper;

    public function __construct(
        Context $context,
        Data $searchNoResultHelper,
        Session $catalogSession,
        StoreManagerInterface $storeManager,
        QueryFactory $queryFactory,
        Resolver $layerResolver,
        SearchHelper $data
    )
    {
        $this->_layerResolver = $layerResolver;
        $this->_queryFactory = $queryFactory;
        $this->_helper = $searchNoResultHelper;
        parent::__construct($context, $catalogSession, $storeManager, $queryFactory, $layerResolver, $data);
    }

    public function execute()
    {
        /*
         * BEGIN: Search no results default page. Please keep it up-to-date
         */
        $this->_layerResolver->create(Resolver::CATALOG_LAYER_SEARCH);
        /* @var $query \Magento\Search\Model\Query */
        $query = $this->_queryFactory->get();

        $query->setStoreId($this->storeManager->getStore()->getId());

        if ($query->getQueryText() != '') {

            if ($this->_objectManager->get(\Magento\CatalogSearch\Helper\Data::class)->isMinQueryLength()) {
                $query->setId(0)->setIsActive(1)->setIsProcessed(1);
            } else {
                $query->saveIncrementalPopularity();

                $redirect = $query->getRedirect();
                if ($redirect && $this->_url->getCurrentUrl() !== $redirect) {
                    $this->getResponse()->setRedirect($redirect);
                    return;
                }
            }
            $this->_objectManager->get(\Magento\CatalogSearch\Helper\Data::class)->checkNotes();

            //$this->_view->loadLayout();
            //$this->_view->renderLayout();
            /*
             * END: Search no results default page. Please keep it up-to-date
             */

            $appliedItems = $this->_layerResolver->get()->getState()->getFilters();
            /**
             * @var $page \Magento\Framework\View\Result\Page
             */
            $page = $this->resultFactory->create('page');
            $layout = $page->getLayout();
            $result = $layout->getBlock('search.result');

            if (!$result->getResultCount() && !count($appliedItems) && !$this->getRequest()->getParam('shopbyAjax')) {
                $noResultBlock = $this->_objectManager->create('Forix\SearchNoResult\Block\NoResult', ['cacheable' => false])->setTemplate('noresult.phtml');
                $noResultBlock->setBlockId('noresult_category_list');
                $layout->addBlock($noResultBlock, 'search.no.result', 'content');

                if (!$noResultBlock->isEnablePageTitleHeadline()) {
                    $layout->unsetElement('page.main.title');
                }
                if (!$noResultBlock->isEnableBreadcrumbs()) {
                    $layout->unsetElement('breadcrumbs');
                }
                if (!$noResultBlock->isEnableWarningMessage()) {
                    $layout->unsetElement('search.result');
                }

                $layout->unsetElement('sidebar.main');
                $layout->unsetElement('sidebar.additional');
                /*
                $layout->unsetElement('result-description');
                $layout->unsetElement('result-footer');
                */

                $page->getConfig()->setPageLayout('1column');
                $page->getConfig()->addBodyClass('page-search-noresult');
                $page->getConfig()->getTitle()->set(__($noResultBlock->getSearchPageTitle(), $query->getQueryText()));
            }
            return $page;
        } else {
            $this->getResponse()->setRedirect($this->_redirect->getRedirectUrl());
        }


    }
}