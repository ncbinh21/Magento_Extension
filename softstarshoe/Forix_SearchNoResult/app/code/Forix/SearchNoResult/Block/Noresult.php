<?php
/**
 * Created by PhpStorm.
 * User: Eric
 * Date: 3/16/2016
 * Time: 10:57 AM
 */

namespace Forix\SearchNoResult\Block;

use Magento\CatalogSearch\Helper\Data;
use Magento\Framework\View\Element\Template\Context;

class Noresult extends \Magento\Framework\View\Element\Template
{
    /**
     * Top menu data tree
     *
     * @var \Magento\Framework\Data\Tree\Node
     */
    protected $_menu;
    private $helper;

    /**
     * Catalog search data
     *
     * @var Data
     */

    protected $catalogSearchData;

    /**
     * Noresult constructor.
     * @param Context $context
     * @param Data $catalogSearchData
     * @param array $data
     */
    public function __construct(
        Context $context,
        Data $catalogSearchData,
        \Forix\SearchNoResult\Helper\Data $searchNoResultHelper,
        array $data = []
    ) {
        $this->catalogSearchData = $catalogSearchData;
        $this->helper = $searchNoResultHelper;
        parent::__construct($context, $data);
    }

    /**
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareLayout()
    {
        $title = $this->getSearchPageTitle();
        $this->pageConfig->getTitle()->set($title);
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
                ['label' => $title, 'title' => $title]
            );
        }

        return parent::_prepareLayout();
    }

    public function getSearchPageTitle()
    {
        return $this->helper->getConfigValue('search_page_title');
    }

    /**
     * Get search query text
     *
     * @return \Magento\Framework\Phrase
     */
    public function getSearchQueryText()
    {
        return __($this->helper->getConfigValue('no_result_text'), $this->catalogSearchData->getEscapedQueryText());
    }

    /**
     * Get note
     *
     * @return \Magento\Framework\Phrase
     */
    public function getNote()
    {
        return $this->helper->getConfigValue('note');
    }

    /**
     * @return \Magento\Framework\Phrase|mixed
     */
    public function getNoResultText()
    {
        if ($this->catalogSearchData->isMinQueryLength()) {
            return __('Minimum Search query length is %1', $this->_getQuery()->getMinQueryLength());
        }
        return $this->_getData('no_result_text');
    }

    /**
     * @return array
     */
    public function getNoteMessages()
    {
        return $this->catalogSearchData->getNoteMessages();
    }

}