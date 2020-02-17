<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 14/09/2018
 * Time: 18:37
 */

namespace Forix\ProductWizard\Controller\Cart;

use Magento\Checkout\Model\Cart as CustomerCart;
use Magento\Framework\App\ResponseInterface;

class Add extends \Magento\Checkout\Controller\Cart
{
    protected $_productManager;
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        \Forix\ProductWizard\Model\ProductManager $productManager,
        CustomerCart $cart
    )
    {
        $this->_productManager = $productManager;
        parent::__construct($context, $scopeConfig, $checkoutSession, $storeManager, $formKeyValidator, $cart);
    }

    /**
     * Execute action based on request and return result
     *
     * Note: Request will be added as operation argument in future
     *
     * @return \Magento\Framework\Controller\ResultInterface|ResponseInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function execute()
    {
        if (!$this->_formKeyValidator->validate($this->getRequest())) {
            $this->messageManager->addError(
                $this->_objectManager->get(\Magento\Framework\Escaper::class)->escapeHtml("Form Key does not validate.")
            );
            return $this->resultRedirectFactory->create()->setPath('*/*/');
        }
        try {
            $selectedProducts = $this->getRequest()->getParam('selected_product');
            if ($selectedProducts) {
                if(!is_array($selectedProducts)){
                    $selectedProducts = explode(',', $selectedProducts);
                }

                $this->cart->addProductsByIds($selectedProducts);

                $this->cart->save();
                if (!$this->_checkoutSession->getNoCartRedirect(true)) {
                    if (!$this->cart->getQuote()->getHasError()) {
                        $message = __('You added these to your shopping cart.');
                        $this->messageManager->addSuccessMessage($message);
                    }
                }
            }
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            if ($this->_checkoutSession->getUseNotice(true)) {
                $this->messageManager->addNotice(
                    $this->_objectManager->get(\Magento\Framework\Escaper::class)->escapeHtml($e->getMessage())
                );
            } else {
                $messages = array_unique(explode("\n", $e->getMessage()));
                foreach ($messages as $message) {
                    $this->messageManager->addError(
                        $this->_objectManager->get(\Magento\Framework\Escaper::class)->escapeHtml($message)
                    );
                }
            }
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __('We can\'t add these items to your shopping cart right now.'));
            $this->_objectManager->get(\Psr\Log\LoggerInterface::class)->critical($e);
        }
        return $this->goBack();
    }
    protected function goBack($backUrl = null)
    {
        $result = [];
        $this->getResponse()->representJson(
            $this->_objectManager->get(\Magento\Framework\Json\Helper\Data::class)->jsonEncode($result)
        );
    }
}