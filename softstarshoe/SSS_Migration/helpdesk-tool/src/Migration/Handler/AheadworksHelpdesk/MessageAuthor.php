<?php
namespace Migration\Handler\AheadworksHelpdesk;

use Migration\ResourceModel\Source;
use Migration\ResourceModel\Destination;
use Migration\ResourceModel\Record;
use Migration\Handler\AbstractHandler;
use Migration\Handler\HandlerInterface;

/**
 * Handler for MessageAuthor
 */
class MessageAuthor extends AbstractHandler implements HandlerInterface
{
    /**#@+
     * Allowed M1 HDU message types
     */
    const TYPE_ESCALATE = 3;
    const TYPE_MESSAGE = 4;
    const TYPE_NOTE = 5;
    /**#@-*/

    /**
     * @var Source
     */
    private $source;

    /**
     * @var Destination
     */
    private $destination;

    /**
     * @param Source $source
     * @param Destination $destination
     */
    public function __construct(
        Source $source,
        Destination $destination
    ) {
        $this->source = $source;
        $this->destination = $destination;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(Record $recordToHandle, Record $oppositeRecord)
    {
        $this->validate($recordToHandle);
        $ticketHistoryId = $recordToHandle->getValue($this->field);
        $ticketHistoryData = $this->getTicketHistoryData($ticketHistoryId);
        $agentId = $ticketHistoryData['agent_id'];
        $sourceTicketId = $ticketHistoryData['source_ticket_id'];
        $messageType = $ticketHistoryData['message_type'];

        $recordToHandle->setValue('author_name', 'unknown');
        $recordToHandle->setValue('author_email', 'unknown');
        $recordToHandle->setValue('created_at', $ticketHistoryData['created_at']);

        if ($agentId) {
            if ($messageType == self::TYPE_NOTE) {
                $recordToHandle->setValue($this->field, 'admin-internal');
            } else {
                $recordToHandle->setValue($this->field, 'admin-reply');
            }
            $agentData = $this->getAgentData($agentId);
            if ($agentData) {
                $recordToHandle->setValue('author_name', $agentData['agent_name']);
                $recordToHandle->setValue('author_email', $agentData['agent_email']);
            }
        } else {
            if ($messageType == self::TYPE_ESCALATE) {
                $content = $recordToHandle->getValue('content');
                $recordToHandle->setValue('content', __('Ticket has been escalated: %1', $content));
            }
            $recordToHandle->setValue($this->field, 'customer-reply');
            $customerData = $this->getCustomerData($sourceTicketId);
            if ($customerData) {
                $recordToHandle->setValue('author_name', $customerData['customer_name']);
                $recordToHandle->setValue('author_email', $customerData['customer_email']);
            }
        }
    }

    /**
     * Get ticket history data
     *
     * @param int $ticketHistoryId
     * @return array|bool
     */
    private function getTicketHistoryData($ticketHistoryId)
    {
        $adapter = $this->source->getAdapter();
        $query = $adapter->getSelect()
            ->from(
                $this->source->addDocumentPrefix('aw_hdu3_ticket_history'),
                ['ticket_id', 'initiator_department_agent_id', 'event_type', 'created_at']
            )
            ->where("id = ?", $ticketHistoryId)
        ;
        $result = $query->getAdapter()->fetchRow($query);
        if ($result) {
            $ticketHistoryData = [
                'agent_id'          => $result['initiator_department_agent_id'],
                'source_ticket_id'  => $result['ticket_id'],
                'message_type'        => $result['event_type'],
                'created_at'        => $result['created_at']
            ];
            return $ticketHistoryData;
        }
        return false;
    }

    /**
     * Get agent data
     *
     * @param int $agentId
     * @return array|bool
     */
    private function getAgentData($agentId)
    {
        $adapter = $this->source->getAdapter();
        $query = $adapter->getSelect()
            ->from(
                $this->source->addDocumentPrefix('aw_hdu3_department_agent'),
                ['name', 'email']
            )
            ->where("id = ?", $agentId)
        ;
        $result = $query->getAdapter()->fetchRow($query);
        if ($result) {
            $agentData = [
                'agent_name'    => $result['name'],
                'agent_email'   => $result['email']
            ];
            return $agentData;
        }
        return false;
    }

    /**
     * Get customer data from the ticket
     *
     * @param int $ticketId
     * @return array|bool
     */
    private function getCustomerData($ticketId)
    {
        $adapter = $this->source->getAdapter();
        $query = $adapter->getSelect()
            ->from(
                $this->source->addDocumentPrefix('aw_hdu3_ticket'),
                ['customer_name', 'customer_email']
            )
            ->where("id = ?", $ticketId)
        ;
        $result = $query->getAdapter()->fetchRow($query);
        if ($result) {
            return $result;
        }
        return false;
    }
}
