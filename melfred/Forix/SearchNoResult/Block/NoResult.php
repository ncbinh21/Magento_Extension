<?php
namespace Forix\SearchNoResult\Block;

use Magento\CatalogSearch\Helper\Data;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

class NoResult extends Template
{
    /**
     * Top menu data tree
     *
     * @var \Magento\Framework\Data\Tree\Node
     */
    protected $_menu;
    private $_helper;

    /**
     * Catalog search data
     *
     * @var Data
     */

    protected $catalogSearchData;

    /**
     * Search No Results constructor.
     * @param Context $context
     * @param Data $catalogSearchData
     * @param \Forix\SearchNoResult\Helper\Data $searchNoResultHelper
     * @param array $data
     */
    public function __construct(
        Context $context,
        Data $catalogSearchData,
        \Forix\SearchNoResult\Helper\Data $searchNoResultHelper,
        array $data = []
    ) {
        $this->catalogSearchData = $catalogSearchData;
        $this->_helper = $searchNoResultHelper;
        parent::__construct($context, $data);
    }

    /**
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareLayout()
    {
        $breadcrumbsTitle = __($this->getBreadcrumbsTitle(), $this->catalogSearchData->getEscapedQueryText());
        $this->pageConfig->getTitle()->set($this->getSearchPageTitle());
        // add Home breadcrumb
        $breadcrumbs = $this->getLayout()->getBlock('breadcrumbs');
        if ($breadcrumbs) {
            $breadcrumbs->addCrumb(
                'home',
                [
                    'label' => __('Home'),
                    'title' => __('Go to Home Page'),
                    'link' => $this->_storeManager->getStore()->getBaseUrl()
                ]
            )->addCrumb(
                'search',
                ['label' => $breadcrumbsTitle, '$breadcrumbsTitle' => $breadcrumbsTitle]
            );
        }

        return parent::_prepareLayout();
    }

    /**
     * @return mixed
     */
    public function getSearchPageTitle()
    {
        return $this->_helper->getConfigValue('page_title');
    }

    /**
     * @return mixed
     */
    public function isEnablePageTitleHeadline(){
        return $this->_helper->getConfigValue('enable_page_title_headline');
    }


    /**
     * Is Enable Warning Message
     *
     * @return mixed
     */
    public function isEnableWarningMessage(){
        return $this->_helper->getConfigValue('enable_warning_message');
    }
    /**
     * Get search query text
     *
     * @return \Magento\Framework\Phrase
     */
    public function getConfiguredNoResultText()
    {
        return __($this->_helper->getConfigValue('no_result_text'), $this->catalogSearchData->getEscapedQueryText());
    }

    /**
     * Get note
     *
     * @return \Magento\Framework\Phrase
     */
    public function getNote()
    {
        return $this->_helper->getConfigValue('note');
    }

    /**
     * Get breadcrumbs title
     *
     * @return \Magento\Framework\Phrase
     */
    public function getBreadcrumbsTitle()
    {
        return $this->_helper->getConfigValue('breadcrumbs_title');
    }

    /**
     * @return boolean
     */
    public function isEnableBreadcrumbs()
    {
        return $this->_helper->getConfigValue('enable_breadcrumbs');
    }

    /**
     * @return \Magento\Framework\Phrase|mixed
     */
    public function getNoResultText()
    {
        if ($this->catalogSearchData->isMinQueryLength()) {
            return __('Minimum Search query length is %1', $this->_getQuery()->getMinQueryLength());
        }
        return $this->getConfiguredNoResultText();
    }

    /**
     * @return array
     */
    public function getNoteMessages()
    {
        return $this->catalogSearchData->getNoteMessages();
    }

    /**
     * @return boolean
     */
    public function isEnableCategory()
    {
        return $this->_helper->getConfigValue('enable_category');
    }

}