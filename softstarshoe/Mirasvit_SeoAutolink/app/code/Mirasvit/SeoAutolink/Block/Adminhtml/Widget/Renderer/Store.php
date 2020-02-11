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



namespace Mirasvit\SeoAutolink\Block\Adminhtml\Widget\Renderer;

class Store extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\Store
{
    /**
     * @var \Mirasvit\SeoAutolink\Model\ResourceModel\Link\CollectionFactory
     */
    protected $linkCollectionFactory;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $resource;

    /**
     * @var \Magento\Backend\Block\Context
     */
    protected $context;

    /**
     * @param \Mirasvit\SeoAutolink\Model\ResourceModel\Link\CollectionFactory $linkCollectionFactory
     * @param \Magento\Framework\App\ResourceConnection                        $resource
     * @param \Magento\Backend\Block\Context                                   $context
     * @param \Magento\Store\Model\System\Store                                $systemStore
     * @param array                                                            $data
     */
    public function __construct(
        \Mirasvit\SeoAutolink\Model\ResourceModel\Link\CollectionFactory $linkCollectionFactory,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Backend\Block\Context $context,
        \Magento\Store\Model\System\Store $systemStore,
        array $data = []
    ) {
        $this->linkCollectionFactory = $linkCollectionFactory;
        $this->resource = $resource;
        $this->context = $context;
        parent::__construct($context, $systemStore, $data);
    }

    /**
     * Render row store views.
     *
     * @param \Magento\Framework\DataObject $template
     *
     * @return string
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function render(\Magento\Framework\DataObject $template)
    {
        $out = '';
        $skipAllStoresLabel = $this->_getShowAllStoresLabelFlag();
        $skipEmptyStoresLabel = $this->_getShowEmptyStoresLabelFlag();
        $origStores = $template->getData($this->getColumn()->getIndex());

        $storeCollection = $this->linkCollectionFactory->create();
        $storeCollection->getSelect()
                ->joinLeft(
                    ['store_table' => $this->resource->getTableName('mst_seoautolink_link_store')],
                    'main_table.link_id = store_table.link_id',
                    []
                )
                ->where('store_table.link_id in (?)', [$origStores])
                ->reset(\Zend_Db_Select::COLUMNS)
                ->columns('store_id', 'store_table');

        $origStores = [];
        foreach ($storeCollection as $store) {
            $origStores[] = $store->getStoreId();
        }

        if ($origStores === null && $template->getStoreName()) {
            $scopes = [];
            foreach (explode("\n", $template->getStoreName()) as $k => $label) {
                $scopes[] = str_repeat('&nbsp;', $k * 3).$label;
            }
            $out .= implode('<br/>', $scopes).__(' [deleted]');

            return $out;
        }

        if (empty($origStores) && !$skipEmptyStoresLabel) {
            return '';
        }
        if (!is_array($origStores)) {
            $origStores = [$origStores];
        }

        if (empty($origStores)) {
            return '';
        } elseif (in_array(0, $origStores) && count($origStores) == 1 && !$skipAllStoresLabel) {
            return __('All Store Views');
        }

        $data = $this->_getStoreModel()->getStoresStructure(false, $origStores);

        foreach ($data as $website) {
            $out .= $website['label'].'<br/>';
            foreach ($website['children'] as $group) {
                $out .= str_repeat('&nbsp;', 3).$group['label'].'<br/>';
                foreach ($group['children'] as $store) {
                    $out .= str_repeat('&nbsp;', 6).$store['label'].'<br/>';
                }
            }
        }

        return $out;
    }
}
