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
 * @version   2.0.64
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Seo\Block\Adminhtml\Duplicateinfo\Renderer;

/**
 * Grid item renderer currency category path
 *
 */
class CategoryPath extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    protected $categoryFactory;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;

    /**
     * @param \Magento\Catalog\Model\CategoryFactory $categoryFactory
     * @param \Magento\Framework\UrlInterface $urlBuilder
     */
    public function __construct(
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Framework\UrlInterface $urlBuilder
    ) {
        $this->categoryFactory = $categoryFactory;
        $this->urlBuilder = $urlBuilder;
    }
    /**
     * Renders grid column
     *
     * @param \Magento\Framework\DataObject $row
     * @return string
     */
    public function render(\Magento\Framework\DataObject $row)
    {
        $preparedPath = '';
        $categoryId = $row->getData($this->getColumn()->getIndex());
        $rowCategory = $this->categoryFactory->create()->load($categoryId);
        $path = $rowCategory->getPath();
        $categoryIds = explode('/', $path);

        foreach ($categoryIds as $id) {
            if ($id == 1) {
                $preparedPath .= 'Root Catalog (ID: 1)';
            } elseif($categoryId == $id){
                $categoryUrl =  $this->urlBuilder->getUrl(
                            'catalog/category/edit/',
                            ['id' => $rowCategory->getId()]
                        );
                $preparedPath .= ' / ' . '"<a href="' . $categoryUrl . '" target="_blank">'
                                . $rowCategory->getName() . ' (ID: ' . $rowCategory->getId() . ')'
                                . "</a>";
            } else {
                $category = $this->categoryFactory->create()->load($id);
                $preparedPath .= ' / ' . $category->getName() . ' (ID: ' . $category->getId() . ')';
            }
        }

        return $preparedPath;
    }
}
