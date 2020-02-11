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
 * @package   mirasvit/module-credit
 * @version   1.0.41
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Credit\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;

class Message extends AbstractHelper
{
    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory
     */
    protected $orderCollectionFactory;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\Creditmemo\CollectionFactory
     */
    protected $orderCreditmemoCollectionFactory;

    /**
     * @var \Magento\Framework\App\Helper\Context
     */
    protected $context;

    /**
     * @var \Magento\Backend\Model\Url
     */
    protected $backendUrlManager;

    /**
     * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory            $orderCollectionFactory
     * @param \Magento\Sales\Model\ResourceModel\Order\Creditmemo\CollectionFactory $orderCreditmemoCollectionFactory
     * @param Context                                                               $context
     * @param \Magento\Backend\Model\Url                                            $backendUrlManager
     */
    public function __construct(
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        \Magento\Sales\Model\ResourceModel\Order\Creditmemo\CollectionFactory $orderCreditmemoCollectionFactory,
        Context $context,
        \Magento\Backend\Model\Url $backendUrlManager
    ) {
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->orderCreditmemoCollectionFactory = $orderCreditmemoCollectionFactory;
        $this->context = $context;
        $this->backendUrlManager = $backendUrlManager;

        parent::__construct($context);
    }

    /**
     * @param array $array
     * @return string
     */
    public function createTransactionMessage($array)
    {
        $arMessage = [];

        if (isset($array['order'])) {
            $order = $array['order'];

            $arMessage[] = __('Order #%1', 'o|' . $order->getIncrementId());
        }

        if (isset($array['creditmemo'])) {
            $memo = $array['creditmemo'];

            $arMessage[] = __('Creditmemo #%1', 'm|' . $memo->getIncrementId());
        }

        return implode(', ', $arMessage);
    }

    /**
     * @param string $message
     * @return string
     */
    public function getBackendTransactionMessage($message)
    {
        return $this->getPreparedTransactionMessage($message, 'adminhtml');
    }

    /**
     * @param string $message
     * @return string
     */
    public function getFrontendTransactionMessage($message)
    {
        return $this->getPreparedTransactionMessage($message, 'frontend');
    }

    /**
     * @param string $message
     * @return string mixed
     */
    public function getEmailTransactionMessage($message)
    {
        return $this->getPreparedTransactionMessage($message, 'email');
    }

    /**
     * @param string $message
     * @param string $type
     * @return string
     */
    public function getPreparedTransactionMessage($message, $type)
    {
        $message = $this->highlightOrdersInMessage($message, $type);
        $message = $this->highlightMemosInMessage($message, $type);

        return $message;
    }

    /**
     * @param string $message
     * @param string $type
     * @return string
     */
    protected function highlightOrdersInMessage($message, $type)
    {
        $orderMatches = [];
        preg_match_all('/#o\|([0-9]*)/is', $message, $orderMatches);

        if (count($orderMatches) && isset($orderMatches[1])) {
            foreach ($orderMatches[1] as $key => $incrementId) {
                $order = $this->orderCollectionFactory->create()
                    ->addFieldToFilter('increment_id', $incrementId)
                    ->getFirstItem();

                $url = false;
                if ($type == 'adminhtml') {
                    $url = $this->backendUrlManager->getUrl(
                        'sales/order/view/',
                        ['order_id' => $order->getId()]
                    );
                } elseif ($type == 'frontend') {
                    $url = $this->context->getUrlBuilder()->getUrl('sales/order/view', ['order_id' => $order->getId()]);
                }

                if ($url) {
                    $replace = "<a href='$url' target='_blank'>#$incrementId</a>";
                } else {
                    $replace = "#$incrementId";
                }

                $message = str_replace($orderMatches[0][$key], $replace, $message);
            }
        }

        return $message;
    }

    /**
     * @param string $message
     * @param string $type
     * @return string
     */
    protected function highlightMemosInMessage($message, $type)
    {
        $memoMatches = [];
        preg_match_all('/#m\|([0-9]*)/is', $message, $memoMatches);

        if (count($memoMatches) && isset($memoMatches[1])) {
            foreach ($memoMatches[1] as $key => $incrementId) {
                $memo = $this->orderCreditmemoCollectionFactory->create()
                    ->addFieldToFilter('increment_id', $incrementId)
                    ->getFirstItem();

                $url = false;
                if ($type == 'adminhtml') {
                    $url = $this->backendUrlManager->getUrl(
                        'sales/creditmemo/view',
                        ['creditmemo_id' => $memo->getId()]
                    );
                } elseif ($type == 'frontend') {
                    $url = $this->context->getUrlBuilder()
                        ->getUrl('sales/order/creditmemo', ['order_id' => $memo->getOrderId()]);
                }

                if ($url) {
                    $replace = "<a href='$url' target='_blank'>#$incrementId</a>";
                } else {
                    $replace = "#$incrementId";
                }

                $message = str_replace($memoMatches[0][$key], $replace, $message);
            }
        }

        return $message;
    }
}
