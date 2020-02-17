<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Forix\OrderComments\Model;

use Magento\Directory\Model\Currency;
use Magento\Framework\Api\AttributeValueFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderStatusHistoryInterface;
use Magento\Sales\Model\Order\Payment;
use Magento\Sales\Model\ResourceModel\Order\Address\Collection;
use Magento\Sales\Model\ResourceModel\Order\Creditmemo\Collection as CreditmemoCollection;
use Magento\Sales\Model\ResourceModel\Order\Invoice\Collection as InvoiceCollection;
use Magento\Sales\Model\ResourceModel\Order\Item\Collection as ImportCollection;
use Magento\Sales\Model\ResourceModel\Order\Payment\Collection as PaymentCollection;
use Magento\Sales\Model\ResourceModel\Order\Shipment\Collection as ShipmentCollection;
use Magento\Sales\Model\ResourceModel\Order\Shipment\Track\Collection as TrackCollection;
use Magento\Sales\Model\ResourceModel\Order\Status\History\Collection as HistoryCollection;


class CustomOrder extends \Magento\Sales\Model\Order
{
    public function getOrderNotes()
    {
        $request_body = file_get_contents('php://input');
        // decode JSON post data into array
        if(!empty($request_body)){
            $data = json_decode($request_body, true);
            // get order comments
            if (isset ($data['paymentMethod']['additional_data']['comments'])) {
                // make sure there is a comment to save
                $orderComments = $data['paymentMethod']['additional_data']['comments'];
                if ($orderComments) {
                    // remove any HTML tags
                    $comment = strip_tags ($orderComments);
                    //$comment = 'ORDER COMMENT:  ' . $comment;
                    return $comment;
                }
            }
        }

        foreach ($this->getStatusHistoryCollection() as $status) {
            if (!$status->isDeleted() && $status->getComment() && $status->getIsVisibleOnFront()) {
                return $status->getComment();
            }
        }
        return "";
    }
}
