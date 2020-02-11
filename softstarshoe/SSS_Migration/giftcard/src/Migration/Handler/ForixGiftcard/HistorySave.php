<?php
/**
 * Created by Nick Howard.
 * Job Title: Magento Developer
 * Project: softstarshoes.local
 */

namespace Migration\Handler\ForixGiftcard;

use Migration\ResourceModel\Destination;
use Migration\ResourceModel\Record;
use Migration\Handler\AbstractHandler;
use Migration\Handler\HandlerInterface;

class HistorySave extends AbstractHandler implements HandlerInterface
{
    /**
     * @var Destination
     */
    private $destination;

    /**
     * @param Destination $destination
     */
    public function __construct(
        Destination $destination
    ) {
        $this->destination = $destination;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(Record $recordToHandle, Record $oppositeRecord)
    {
        //$this->validate($recordToHandle);
        $data = [];
        /** @var \Magento\Framework\DB\Adapter\Pdo\Mysql $adapter */

        if($recordToHandle->getValue('action') == 1){
            $data['initial_balance'] = $recordToHandle->getValue('balance_amount');
            if(strpos($recordToHandle->getValue('comment'),'Order #') !== false){
                $incrementId = str_replace('Order #','',$recordToHandle->getValue('comment'));
                $incrementId = rtrim($incrementId,'.');
                if($orderId = $this->getOrderId($incrementId)){
                    $data['order_id'] = $orderId;
                    if($giftCardInfos = $this->getGiftCardInfo($orderId)){
                        $giftCardCode = $this->getGiftCardCode($recordToHandle->getValue('giftcard_id'));
                        foreach($giftCardInfos as $_giftCardInfo){

                            $_giftCardInfo = unserialize($_giftCardInfo);
                            if(isset($_giftCardInfo['giftcard_created_codes']) && is_array($_giftCardInfo['giftcard_created_codes']) && in_array($giftCardCode,$_giftCardInfo['giftcard_created_codes'])){
                                $data['sender_name']    =   $_giftCardInfo['giftcard_sender_name'];
                                $data['sender_email']    =   $_giftCardInfo['giftcard_sender_email'];
                                $data['recipient_name']    =   $_giftCardInfo['giftcard_recipient_name'];
                                $data['recipient_email']    =   $_giftCardInfo['giftcard_recipient_email'];
                            }
                        }
                        
                    }
                }
            }
        }
        if($recordToHandle->getValue('action') == 4){
            preg_match("'Recipient: (.*?) <(.*?)>\\. (.*?)\\.'si", $recordToHandle->getValue('comment'), $matches);
            if(count($matches) == 4){
                $data['sender_name']    =   $matches[3];
                $data['recipient_name']    =   $matches[1];
                $data['recipient_email']    =   $matches[2];
            }
        }

        $adapter = $this->destination->getAdapter()->getSelect()->getAdapter();
        if(count($data) >0){
            $adapter->update(
                $this->destination->addDocumentPrefix('aw_giftcard'),
                $data,
                ["id = ?"=>$recordToHandle->getValue('giftcard_id')]
            );
        }

    }

    /**
     * Get agent name
     *
     * @param int $agentId
     * @return string|bool
     */
    private function getAgentName($agentId)
    {
        $adapter = $this->destination->getAdapter();
        $query = $adapter->getSelect()
            ->from(
                $this->destination->addDocumentPrefix('admin_user'),
                ['firstname', 'lastname']
            )
            ->where("user_id = ?", $agentId)
        ;
        $result = $query->getAdapter()->fetchRow($query);
        if ($result) {
            return $result['firstname'] . ' ' . $result['lastname'];
        }
        return false;
    }

    /**
     * Get order increment id
     *
     * @param int $orderId
     * @return string|bool
     */
    private function getOrderId($incrementId)
    {
        $adapter = $this->destination->getAdapter();
        $query = $adapter->getSelect()
            ->from(
                $this->destination->addDocumentPrefix('sales_order'),
                ['entity_id']
            )
            ->where("increment_id = ?", $incrementId)
        ;
        $result = $query->getAdapter()->fetchOne($query);
        if ($result) {
            return $result;
        }
        return false;
    }
    private function getGiftCardInfo($orderId)
    {
        $adapter = $this->destination->getAdapter();
        $query = $adapter->getSelect()
            ->from(
                $this->destination->addDocumentPrefix('sales_order_item'),
                ['product_options']
            )
            ->where("order_id = ?", $orderId)
            ->where("product_type = ?",'giftcard')
        ;
        $result = $query->getAdapter()->fetchCol($query);
        if ($result) {
            return $result;
        }
        return false;
    }
    private function getGiftCardCode($giftCardId)
    {
        $adapter = $this->destination->getAdapter();
        $query = $adapter->getSelect()
            ->from(
                $this->destination->addDocumentPrefix('aw_giftcard'),
                ['code']
            )
            ->where("id = ?", $giftCardId)
        ;
        $result = $query->getAdapter()->fetchOne($query);
        if ($result) {
            return $result;
        }
        return false;
    }
}
