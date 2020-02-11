<?php
namespace Migration\Handler\AheadworksHelpdesk;

use Migration\ResourceModel\Destination;
use Migration\ResourceModel\Record;
use Migration\Handler\AbstractHandler;
use Migration\Handler\HandlerInterface;

/**
 * Handler for MessageSave
 */
class MessageSave extends AbstractHandler implements HandlerInterface
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

        $ticketId = $recordToHandle->getValue('ticket_id');
        $ticketGridData = $this->getRecordDataToUpdate($ticketId);
        if ($ticketGridData) {
            $ticketGridData['last_reply_type'] = $recordToHandle->getValue('type');
            $ticketGridData['last_reply_by'] = $recordToHandle->getValue('author_name');
            $ticketGridData['last_reply_date'] = $recordToHandle->getValue('created_at');

            if (!isset($ticketGridData['agent_messages']) && !isset($ticketGridData['customer_messages'])) {
                $ticketGridData['first_message_content'] = $recordToHandle->getValue('content');
                $ticketGridData['agent_messages'] = 0;
                $ticketGridData['customer_messages'] = 0;
            }

            if ($recordToHandle->getValue('type') =='admin-reply') {
                $ticketGridData['agent_messages']++;
            } else {
                $ticketGridData['customer_messages']++;
            }

            $adapter = $this->destination->getAdapter()->getSelect()->getAdapter();
            $adapter->insertOnDuplicate(
                $this->destination->addDocumentPrefix('aw_helpdesk_ticket_grid_flat'),
                $ticketGridData,
                []
            );
        }
    }

    /**
     * Get ticket grid flat data by ticket id
     *
     * @param int $ticketId
     * @return string|bool
     */
    private function getRecordDataToUpdate($ticketId)
    {
        $adapter = $this->destination->getAdapter();
        $query = $adapter->getSelect()
            ->from(
                $this->destination->addDocumentPrefix('aw_helpdesk_ticket_grid_flat'),
                ['*']
            )
            ->where("ticket_id = ?", $ticketId)
        ;
        $result = $query->getAdapter()->fetchRow($query);
        if ($result) {
            return $result;
        }
        return false;
    }
}
