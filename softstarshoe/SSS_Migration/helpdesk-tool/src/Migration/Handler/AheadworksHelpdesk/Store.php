<?php
namespace Migration\Handler\AheadworksHelpdesk;

use Migration\ResourceModel\Destination;
use Migration\ResourceModel\Record;
use Migration\Handler\AbstractHandler;
use Migration\Handler\HandlerInterface;

/**
 * Handler for Store
 */
class Store extends AbstractHandler implements HandlerInterface
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
        $storeId = $recordToHandle->getValue($this->field);
        $departmentId = $recordToHandle->getValue('department_id');

        if ($storeId == 0) {
            $adapter = $this->destination->getAdapter();
            $query = $adapter->getSelect()
                ->from($this->destination->addDocumentPrefix('aw_helpdesk_department_website'), ['website_id'])
                ->where("department_id = ?", $departmentId)
            ;
            $result = $query->getAdapter()->fetchRow($query);
            $websiteId = $result['website_id'];

            $adapter = $this->destination->getAdapter();
            $query = $adapter->getSelect()
                ->from($this->destination->addDocumentPrefix('store'), ['store_id'])
                ->where("website_id = ?", $websiteId)
                ->where("is_active = 1")
                ->order('store_id ASC')
            ;
            $result = $query->getAdapter()->fetchRow($query);
            $storeId = $result['store_id'];
            $recordToHandle->setValue($this->field, $storeId);
        }
    }
}
