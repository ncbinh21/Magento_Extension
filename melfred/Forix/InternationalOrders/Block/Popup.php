<?php
/**
 * Created by Mercurial
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 */

namespace Forix\InternationalOrders\Block;

use Magento\Framework\View\Element\Template;

class Popup extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Forix\InternationalOrders\Helper
     */
    protected $helper;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var product
     */
    protected $product;

    /**
     * @var \Forix\Product\Helper\Data
     */
    protected $dataHelper;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;

    /**
     * Popup constructor.
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Forix\InternationalOrders\Helper\Helper $helper
     * @param Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Forix\Product\Helper\Data $dataHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\App\Request\Http $request,
        \Forix\InternationalOrders\Helper\Helper $helper,
        Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Forix\Product\Helper\Data $dataHelper,
        array $data = []
    ) {
        $this->request = $request;
        $this->dataHelper = $dataHelper;
        $this->registry = $registry;
        $this->helper = $helper;
        parent::__construct($context, $data);
    }

    /**
     * @return mixed|null
     */
    public function isLogin()
    {
        return $this->helper->isCustomerLogin();
    }

    /**
     * @return string
     */
    public function isBackOrder() {
        if ($this->_request->getFullActionName() == 'catalog_product_view') {
            if (is_null($this->product)) {
                $this->product = $this->registry->registry('product');
                if (!$this->product || !$this->product->getId()) {
                    return false;
                }
            }
            if ($this->product->isAvailable()) {
                return !$this->dataHelper->checkInStock($this->product);
            }
        }
        return false;
    }
}