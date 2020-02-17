<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 15/08/2018
 * Time: 15:20
 */

namespace Forix\CategoryCustom\Block\Product;
class View extends \Magento\Catalog\Block\Product\AbstractProduct
{
    protected $_catHelper;

    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Forix\CategoryCustom\Helper\Data $catHelper,
        array $data = []
    )
    {
        parent::__construct($context, $data);
        $this->_catHelper = $catHelper;
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $jsLayout = $this->jsLayout;
        $jsLayout['components']['back_to_category']['config']['back_data'] = $this->getBackData()->getData();
        $this->jsLayout = $jsLayout;
    }

    protected function retryProductCategory()
    {
        if (!$this->getData('retry_category')) {
            $category = $this->getProduct()->getCategory();
            if (null === $category) {
                $product = $this->getProduct();
                $categoryIds = $product->getCategoryIds();
                if (isset($categoryIds[0])) {
                    $category = $this->_catHelper->getSingleCategory($categoryIds[0]);
                }
            }
            $this->setData('retry_category', $category);
        }
        return $this->getData('retry_category');
    }

    public function getBackData()
    {
        $data = [
            'url' => $this->getBackUrl(),
            'name' => $this->getName()
        ];
        $this->_session->getData('filter_ground_condition', true);
        return new \Magento\Framework\DataObject($data);
    }

    protected function getName()
    {
        if ($backData = $this->_session->getData('filter_ground_condition')) {
            return $backData['current_ground_condition']['name'];
        } else {
            if($this->retryProductCategory()) {
                return $this->retryProductCategory()->getName();
            }
            return 'Homepage';
        }
    }

    protected function getBackUrl()
    {
        if ($backData = $this->_session->getData('filter_ground_condition')) {
            return $backData['current_ground_condition']['url'];
        } else {
            if($this->retryProductCategory()) {
                return $this->retryProductCategory()->getUrl();
            }
            return $this->getBaseUrl();
        }
    }
}