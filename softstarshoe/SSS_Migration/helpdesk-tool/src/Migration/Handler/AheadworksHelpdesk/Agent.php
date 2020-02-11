<?php
namespace Migration\Handler\AheadworksHelpdesk;

use Migration\ResourceModel\Source;
use Migration\ResourceModel\Destination;
use Migration\ResourceModel\Record;
use Migration\Handler\AbstractHandler;
use Migration\Handler\HandlerInterface;

/**
 * Handler for Agent
 */
class Agent extends AbstractHandler implements HandlerInterface
{
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
        $agentId = $recordToHandle->getValue($this->field);
        $agentEmail = $this->getSourceStoreAgentEmail($agentId);
        if ($agentEmail) {
            $destAgentId = $this->getDestStoreAgentId($agentEmail);
            if ($destAgentId) {
                $recordToHandle->setValue($this->field, $destAgentId);
                return;
            }
        }
        $recordToHandle->setValue($this->field, 0);
    }

    /**
     * Get agent email from the source store
     *
     * @param int $agentId
     * @return string|false
     */
    private function getSourceStoreAgentEmail($agentId)
    {
        $adapter = $this->source->getAdapter();
        $query = $adapter->getSelect()
            ->from($this->destination->addDocumentPrefix('aw_hdu3_department_agent'), ['email'])
            ->where("id = ?", $agentId)
        ;
        $result = $query->getAdapter()->fetchRow($query);
        if ($result) {
            return $result['email'];
        }
        return false;
    }

    /**
     * Get agent id from the destination store
     *
     * @param string $email
     * @return int|bool
     */
    private function getDestStoreAgentId($email)
    {
        $adapter = $this->destination->getAdapter();
        $query = $adapter->getSelect()
            ->from($this->destination->addDocumentPrefix('admin_user'), ['user_id'])
            ->where("email = ?", $email)
        ;
        $result = $query->getAdapter()->fetchRow($query);
        if ($result) {
            return (int)$result['user_id'];
        }
        return false;
    }
}
