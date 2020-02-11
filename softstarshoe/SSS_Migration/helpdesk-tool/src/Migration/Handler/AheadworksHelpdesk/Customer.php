<?php
namespace Migration\Handler\AheadworksHelpdesk;

use Migration\ResourceModel\Destination;
use Migration\ResourceModel\Record;
use Migration\Handler\AbstractHandler;
use Migration\Handler\HandlerInterface;

/**
 * Handler for Customer
 */
class Customer extends AbstractHandler implements HandlerInterface
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
        $this->validate($recordToHandle);
        $customerEmail = $recordToHandle->getValue($this->field);

        $adapter = $this->destination->getAdapter();
        $query = $adapter->getSelect()
            ->from($this->destination->addDocumentPrefix('customer_entity'), ['entity_id', 'store_id'])
            ->where("email = ?", $customerEmail)
        ;
        $result = $query->getAdapter()->fetchAll($query);
        if ($result) {
            $customerStoreId = $recordToHandle->getValue('store_id');
            foreach ($result as $customer) {
                if ($customer['store_id'] == $customerStoreId) {
                    $recordToHandle->setValue('customer_id', $customer['entity_id']);
                    return;
                }
            }
            $customer = reset($result);
            $recordToHandle->setValue('customer_id', $customer['entity_id']);
        }
    }
}
