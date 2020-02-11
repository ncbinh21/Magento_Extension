<?php
namespace Forix\Checkout\Controller\Items;

use Magento\Framework\App\Action\Context;
use Magento\Checkout\Model\Cart as CustomerCart;

class RemoveAjax extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Checkout\Model\Cart
     */
    protected $cart;

    /**
     * RemoveAjax constructor.
     * @param Context $context
     * @param CustomerCart $cart
     */
    public function __construct(
        Context $context,
        CustomerCart $cart
    ) {
        $this->cart = $cart;
        parent::__construct($context);
    }

    public function execute()
    {
        $listItemId = $this->getRequest()->getParam('listItemId');
        if($listItemId) {
            foreach ($listItemId as $itemId) {
                if ($itemId) {
                    $this->cart->removeItem($itemId);
                }
            }
            $this->cart->save();
        }
        $response = [
            'success' => true,
        ];

        $this->getResponse()->representJson(
            $this->_objectManager->get('Magento\Framework\Json\Helper\Data')->jsonEncode($response)
        );
    }
}