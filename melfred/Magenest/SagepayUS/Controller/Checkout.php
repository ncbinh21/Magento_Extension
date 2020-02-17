<?php
/**
 * Created by PhpStorm.
 * User: hiennq
 * Date: 12/22/16
 * Time: 11:14
 */

namespace Magenest\SagepayUS\Controller;

use Magenest\SagepayUS\Model\Vault;

abstract class Checkout extends \Magento\Framework\App\Action\Action
{

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;
    protected $orderFactory;
    protected $jsonFactory;
    protected $configHelper;
    protected $logger;
    protected $_storeManager;
    protected $vaultFactory;
    /**
     * @var \Magento\Framework\Data\Form\FormKey\Validator
     */
    protected $_formKeyValidator;

    public function __construct(
        \Magenest\SagepayUS\Model\VaultFactory $vaultFactory,
        \Magento\Framework\App\Action\Context $context,
        \Magento\Payment\Helper\Data $paymentHelper,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Checkout\Helper\Data $checkoutData,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Sales\Model\Order\Email\Sender\OrderSender $orderSender,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magenest\SagepayUS\Helper\ConfigHelper $configHelper,
        \Magenest\SagepayUS\Helper\Logger $logger,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        $params = []
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->orderFactory = $orderFactory;
        $this->orderSender = $orderSender;
        $this->configHelper = $configHelper;
        $this->jsonFactory = $resultJsonFactory;
        $this->logger = $logger;
        $this->_storeManager = $storeManager;
        $this->_formKeyValidator = $formKeyValidator;
        parent::__construct($context);
        $this->vaultFactory = $vaultFactory;
    }
}
