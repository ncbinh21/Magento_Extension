<?php

namespace Forix\Custom\Rewrite\Magento\Catalog\Block\Category;

class View extends \Magento\Catalog\Block\Category\View
{
    protected function _prepareLayout()
    {
        \Magento\Framework\View\Element\Template::_prepareLayout();

        $this->getLayout()->createBlock(\Magento\Catalog\Block\Breadcrumbs::class);

        $category = $this->getCurrentCategory();
        if ($category) {
            $title = $category->getMetaTitle();
            if ($title) {
                $this->pageConfig->getTitle()->set($title);
            }
            $description = $category->getMetaDescription();
            if ($description) {
                $this->pageConfig->setDescription($description);
            }
            $keywords = $category->getMetaKeywords();
            if ($keywords) {
                $this->pageConfig->setKeywords($keywords);
            }
            if ($this->_categoryHelper->canUseCanonicalTag()) {
                if(!strpos($category->getUrl(), 'ground-condition') !== false){
                    $this->pageConfig->addRemotePageAsset(
                        $category->getUrl(),
                        'canonical',
                        ['attributes' => ['rel' => 'canonical']]
                    );
                }
            }

            $pageMainTitle = $this->getLayout()->getBlock('page.main.title');
            if ($pageMainTitle) {
                $pageMainTitle->setPageTitle($this->getCurrentCategory()->getName());
            }
        }

        return $this;
    }
}