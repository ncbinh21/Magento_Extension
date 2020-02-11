<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-seo
 * @version   2.0.24
 * @copyright Copyright (C) 2017 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Seo\Block\Adminhtml\Duplicateinfo;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Magento\Backend\Block\Widget\Context
     */
    protected $context;

    /**
     * @var \Magento\Backend\Helper\Data
     */
    protected $backendHelper;

    /**
     * @var \Magento\UrlRewrite\Model\ResourceModel\UrlRewriteCollection
     */
    protected $urlRewriteCategory;

    /**
     * @var \Mirasvit\Seo\Model\Removecategorypath\Validate
     */
    protected $validate;

    /**
     * @param \Magento\Backend\Block\Widget\Context                        $context
     * @param \Magento\Backend\Helper\Data                                 $backendHelper
     * @param \Magento\UrlRewrite\Model\ResourceModel\UrlRewriteCollection $urlRewrite
     * @param \Mirasvit\Seo\Model\Removecategorypath\Validate              $validate
     * @param array                                                        $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\UrlRewrite\Model\ResourceModel\UrlRewriteCollection $urlRewrite,
        \Mirasvit\Seo\Model\Removecategorypath\Validate $validate,
        array $data = []
    ) {
        $this->context = $context;
        $this->backendHelper = $backendHelper;
        $this->urlRewriteCategory = $urlRewrite->addFieldToFilter('entity_type', 'category')
                                                ->addFieldToFilter('redirect_type', 0);
        $this->validate = $validate;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('duplicateinfoGrid');
        $this->setDefaultSort('store_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    /**
     * @return $this
     */
    protected function _prepareCollection()
    {
        $collection = $this->urlRewriteCategory;

        $duplicate = [];
        foreach ($collection as $categoryRewrite) {
            $categoryId = $categoryRewrite->getEntityId();
            $requestPath = $categoryRewrite->getRequestPath();
            $storeId = $categoryRewrite->getStoreId();
            $rewrite[$storeId][$categoryId] = $requestPath;
        }

        foreach ($rewrite as $storeId => $categoryData) {
            if ($duplicateData = $this->validate->getDuplicateUrls($categoryData, true)) {
                $duplicate[$storeId] = $duplicateData;
            }
        }

        if ($duplicate) {
            foreach ($duplicate as $storeId => $duplicateData) {
                $storeCategories = '(' . implode(',', array_keys($duplicateData)) . ')';
                $where[] = "(store_id = $storeId AND entity_id IN $storeCategories)";
            }

            $collection->getSelect()->where(implode(' OR ', $where));
        } else {
            $collection = $collection->addFieldToFilter('entity_type', 'empty');
        }

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * @return $this
     * @throws \Exception
     */
    protected function _prepareColumns()
    {
        $this->addColumn('store_id', [
                'header' => __('Store Id'),
                'align' => 'right',
                'width' => '50px',
                'index' => 'store_id',
            ]);

        $this->addColumn('category_path', [
                'header' => __('Category Path'),
                'index' => 'entity_id',
                'renderer' => 'Mirasvit\Seo\Block\Adminhtml\Duplicateinfo\Renderer\CategoryPath',
                'filter' => false,
                'sortable' => false,
            ]);

        $this->addColumn('entity_id', [
                'header' => __('Category Id'),
                'align' => 'left',
                'index' => 'entity_id',
            ]);

        $this->addColumn('request_path', [
                'header' => __('Current request path'),
                'align' => 'left',
                'index' => 'request_path',
            ]);

        $this->addColumn('url_key', [
                'header' => __('URL key'),
                'index' => 'request_path',
                'renderer' => 'Mirasvit\Seo\Block\Adminhtml\Duplicateinfo\Renderer\UrlKey',
                'filter' => false,
                'sortable' => false,
            ]);

        return parent::_prepareColumns();
    }

}
