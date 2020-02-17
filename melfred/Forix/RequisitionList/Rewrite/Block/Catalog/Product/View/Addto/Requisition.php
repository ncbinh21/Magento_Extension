<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Forix\RequisitionList\Rewrite\Block\Catalog\Product\View\Addto;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Framework\View\Element\Template\Context;
use Magento\Catalog\Block\Product\View;

/**
 * Requisition block
 */
class Requisition extends \Magento\RequisitionList\Block\Catalog\Product\View\Addto\Requisition
{
    /**
     * @var HttpContext
     */
    private $httpContext;

    /**
     * @var View
     */
    private $productView;

    /**
     * Constructor
     *
     * @param Context $context
     * @param HttpContext $httpContext
     * @param View $productView
     * @param array $data
     */
    public function __construct(
        Context $context,
        HttpContext $httpContext,
        View $productView,
        array $data = []
    ) {
        parent::__construct($context, $httpContext, $productView, $data);
        $this->httpContext = $httpContext;
		$this->productView = $productView;

    }

	/**
	 * Get Current Product.
	 *
	 * @return ProductInterface
	 */
	public function getProduct()
	{
		return $this->productView->getProduct();
	}

    /**
     * {@inheritdoc}
     */
    protected function _toHtml()
    {
    	if ($this->getRequest()->getFullActionName() == 'requisition_list_item_configure') {
			$this->setTemplate("Magento_RequisitionList::item/configure/addto.phtml");
		}
        $isCustomerLoggedIn = $this->httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_AUTH);
        return $isCustomerLoggedIn ? parent::_toHtml() : '';
    }
}
