<?php
namespace Migration\Handler\AheadworksHelpdesk;

use Migration\ResourceModel\Destination;
use Migration\ResourceModel\Record;
use Migration\Handler\AbstractHandler;
use Migration\Handler\HandlerInterface;

/**
 * Handler for Order
 */
class Order extends AbstractHandler implements HandlerInterface
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
        $orderIncrementId = $recordToHandle->getValue($this->field);

        $adapter = $this->destination->getAdapter();
        $query = $adapter->getSelect()
            ->from($this->destination->addDocumentPrefix('sales_order'), ['entity_id'])
            ->where("increment_id = ?", $orderIncrementId)
        ;
        $result = $query->getAdapter()->fetchRow($query);

        if ($result) {
            $recordToHandle->setValue($this->field, $result['entity_id']);
            return;
        }

        $recordToHandle->setValue($this->field, 0);
    }
}
