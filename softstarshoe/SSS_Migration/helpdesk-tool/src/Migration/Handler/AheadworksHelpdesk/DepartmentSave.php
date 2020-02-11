<?php
namespace Migration\Handler\AheadworksHelpdesk;

use Migration\ResourceModel\Source;
use Migration\ResourceModel\Destination;
use Migration\ResourceModel\Record;
use Migration\Handler\AbstractHandler;
use Migration\Handler\HandlerInterface;
use Aheadworks\Helpdesk\Api\Data\DepartmentPermissionInterface;

/**
 * Handler for DepartmentSave
 */
class DepartmentSave extends AbstractHandler implements HandlerInterface
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
        $departmentId = $recordToHandle->getValue($this->field);
        $storeIds = $recordToHandle->getValue('is_visible');

        $recordToHandle->setValue('is_visible', 0);
        $adapter = $this->destination->getAdapter()->getSelect()->getAdapter();
        $adapter->insertOnDuplicate(
            $this->destination->addDocumentPrefix('aw_helpdesk_department'),
            $recordToHandle->getData(),
            []
        );

        $storeIds = explode(',', $storeIds);
        if (is_array($storeIds)) {
            if (in_array(0, $storeIds)) {
                $websiteIds = $this->getAllWebsiteIds();
            } else {
                $websiteIds = $this->getWebsiteIds($storeIds);
            }
            if ($websiteIds) {
                foreach ($websiteIds as $websiteId) {
                    $adapter->insertOnDuplicate(
                        $this->destination->addDocumentPrefix('aw_helpdesk_department_website'),
                        [
                            'department_id' => $departmentId,
                            'website_id' => $websiteId
                        ],
                        []
                    );
                }
            }
            $permissionRows = [];
            $permissionRows[] = [
                'department_id' => $departmentId,
                'role_id' => DepartmentPermissionInterface::ALL_ROLES_ID,
                'type' => DepartmentPermissionInterface::TYPE_VIEW
            ];
            $permissionRows[] = [
                'department_id' => $departmentId,
                'role_id' => DepartmentPermissionInterface::ALL_ROLES_ID,
                'type' => DepartmentPermissionInterface::TYPE_UPDATE
            ];
            $permissionRows[] = [
                'department_id' => $departmentId,
                'role_id' => DepartmentPermissionInterface::ALL_ROLES_ID,
                'type' => DepartmentPermissionInterface::TYPE_ASSIGN
            ];
            $adapter->insertMultiple(
                $this->destination->addDocumentPrefix('aw_helpdesk_department_permission'),
                $permissionRows
            );
        }
    }

    /**
     * Get website ids from the source store
     *
     * @param int[] $storeIds
     * @return int[]|bool
     */
    private function getWebsiteIds($storeIds)
    {
        $adapter = $this->source->getAdapter();
        $query = $adapter->getSelect()
            ->from(
                $this->source->addDocumentPrefix('core_store'),
                ['website_id']
            )
            ->where("store_id IN (?)", $storeIds)
        ;
        $result = $query->getAdapter()->fetchAll($query);
        if ($result) {
            $websiteIds = [];
            foreach ($result as $website) {
                if (!in_array((int)$website['website_id'], $websiteIds)) {
                    $websiteIds[] = (int)$website['website_id'];
                }
            }
            return $websiteIds;
        }
        return false;
    }

    /**
     * Get all website ids from the destination store
     *
     * @return int[]|bool
     */
    private function getAllWebsiteIds()
    {
        $adapter = $this->destination->getAdapter();
        $query = $adapter->getSelect()
            ->from(
                $this->destination->addDocumentPrefix('store_website'),
                ['website_id']
            )
            ->where("website_id != 0")
        ;
        $result = $query->getAdapter()->fetchAll($query);
        if ($result) {
            $websiteIds = [];
            foreach ($result as $website) {
                $websiteIds[] = (int)$website['website_id'];
            }
            return $websiteIds;
        }
        return false;
    }
}
