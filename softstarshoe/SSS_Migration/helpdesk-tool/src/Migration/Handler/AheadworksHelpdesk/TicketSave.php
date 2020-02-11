<?php
namespace Migration\Handler\AheadworksHelpdesk;

use Migration\ResourceModel\Destination;
use Migration\ResourceModel\Record;
use Migration\Handler\AbstractHandler;
use Migration\Handler\HandlerInterface;

/**
 * Handler for TicketSave
 */
class TicketSave extends AbstractHandler implements HandlerInterface
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

        $agentId = $recordToHandle->getValue('agent_id');
        $agentName = $this->getAgentName($agentId);
        $orderIncrementId = $this->getIncrementId($recordToHandle->getValue('order_id'));

        $ticketGridData = [
            'ticket_id' => $recordToHandle->getValue('id'),
            'agent_id' => $agentId,
            'agent_name' => $agentName ? $agentName : 'Unassigned',
            'order_increment_id' => $orderIncrementId ? $orderIncrementId : 'Unassigned',

        ];

        $adapter = $this->destination->getAdapter()->getSelect()->getAdapter();
        $adapter->insertOnDuplicate(
            $this->destination->addDocumentPrefix('aw_helpdesk_ticket_grid_flat'),
            $ticketGridData,
            []
        );
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
    private function getIncrementId($orderId)
    {
        $adapter = $this->destination->getAdapter();
        $query = $adapter->getSelect()
            ->from(
                $this->destination->addDocumentPrefix('sales_order'),
                ['increment_id']
            )
            ->where("entity_id = ?", $orderId)
        ;
        $result = $query->getAdapter()->fetchRow($query);
        if ($result) {
            return $result['increment_id'];
        }
        return false;
    }
}
