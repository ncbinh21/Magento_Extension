<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Helpdesk\Model\Source\Ticket;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\User\Model\ResourceModel\User\CollectionFactory as UserCollectionFactory;
use Aheadworks\Helpdesk\Helper\Config as ConfigHelper;
use Aheadworks\Helpdesk\Api\DepartmentRepositoryInterface;
use Aheadworks\Helpdesk\Api\Data\DepartmentInterface;
use Aheadworks\Helpdesk\Api\Data\DepartmentPermissionInterface;

/**
 * Class Agent
 * @package Aheadworks\Helpdesk\Model\Source\Ticket
 */
class Agent implements OptionSourceInterface
{
    /**
     * Agent value of unassigned ticket
     */
    const UNASSIGNED_VALUE = '0';

    /**
     * Inactive label
     */
    const INACTIVE_LABEL = '(Inactive)';

    /**
     * User collection factory
     *
     * @var UserCollectionFactory
     */
    private $userCollectionFactory;

    /**
     * @var ConfigHelper
     */
    private $configHelper;

    /**
     * @var DepartmentRepositoryInterface
     */
    private $departmentRepository;

    /**
     * @param UserCollectionFactory $userCollectionFactory
     * @param ConfigHelper $configHelper
     * @param DepartmentRepositoryInterface $departmentRepository
     */
    public function __construct(
        UserCollectionFactory $userCollectionFactory,
        ConfigHelper $configHelper,
        DepartmentRepositoryInterface $departmentRepository
    ) {
        $this->userCollectionFactory = $userCollectionFactory;
        $this->configHelper = $configHelper;
        $this->departmentRepository = $departmentRepository;
    }

    /**
     * Get option array
     *
     * @return array
     */
    public function getOptionArray()
    {
        $userCollection = $this->getUserCollection();
        $userOptions = [];
        foreach ($userCollection->getItems() as $item) {
            if ($item->getIsActive()) {
                $userOptions[(string)$item->getUserId()] = $item->getUserFullname();
            }
        }
        return $userOptions;
    }

    /**
     * Get option array for filter
     *
     * @return array
     */
    public function getAvailableOptionsForFilter()
    {
        $userCollection = $this->getUserCollection();
        $unassigned = [self::UNASSIGNED_VALUE => __('Unassigned')];
        $userOptions = [];
        foreach ($userCollection->getItems() as $item) {
            $userOptions[(string)$item->getUserId()] = $item->getUserFullname();
        }
        return $unassigned + $userOptions;
    }

    /**
     * Get available options
     * @return array
     */
    public function getAvailableOptions()
    {
        $allOptions = $this->getOptionArray();
        $availableAgents = $this->getAvailableAgents();

        $result = [];
        $unassigned = [self::UNASSIGNED_VALUE => __('Unassigned')];
        if (!$availableAgents) {
            $result = $allOptions;
            return $unassigned + $result;
        }
        foreach ($availableAgents as $agentId) {
            if (false === array_key_exists($agentId, $allOptions)) {
                continue;
            }
            $result[$agentId] = $allOptions[$agentId];
        }
        return $unassigned + $result;
    }

    /**
     * Get user collection
     *
     * @return mixed
     */
    protected function getUserCollection()
    {
        $userCollection = $this->userCollectionFactory->create();
        $userCollection->getSelect()->reset(\Magento\Framework\DB\Select::COLUMNS);
        $userCollection
            ->getSelect()
            ->columns([
                'user_id' => 'main_table.user_id',
                'user_fullname' => 'CONCAT(main_table.firstname, " ", main_table.lastname)',
                'is_active' => 'main_table.is_active'
            ]);
        $this->addInactiveLabelToUserName($userCollection);

        return $userCollection;
    }

    /**
     * To option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        $userCollection = $this->getUserCollection();
        $userList = [];
        foreach ($userCollection->getItems() as $item) {
            if ($item->getIsActive()) {
                $userList[] = ['value' => $item->getUserId(), 'label' => $item->getUserFullname()];
            }
        }

        return $userList;
    }

    /**
     * Get available options for specified department id
     *
     * @param int $departmentId
     * @param int $agentToSkip
     * @return []
     */
    public function getAvailableOptionsForDepartment($departmentId, $agentToSkip = 0)
    {
        try {
            /** @var DepartmentInterface $department */
            $department = $this->departmentRepository->getById($departmentId);
            $updateRoleIds = $department->getPermissions()->getUpdateRoleIds();

            $userCollection = $this->getUserCollection();
            $allUsers = in_array(DepartmentPermissionInterface::ALL_ROLES_ID, $updateRoleIds);
            $userList = [];
            foreach ($userCollection->getItems() as $user) {
                if (!$allUsers) {
                    $userRoles = $user->getRoles();
                    if (count(array_intersect($userRoles, $updateRoleIds)) == 0) {
                        continue;
                    }
                }
                if ($user->getIsActive() || $user->getUserId() == $agentToSkip) {
                    $userList[(string)$user->getUserId()] = $user->getUserFullname();
                }
            }

            $availableAgents = $this->getAvailableAgents();
            $result = [];
            $unassigned = [self::UNASSIGNED_VALUE => __('Unassigned')];
            if (!$availableAgents) {
                $result = $userList;
                return $unassigned + $result;
            }
            foreach ($userList as $agentId => $agentName) {
                if (false === in_array($agentId, $availableAgents) && $agentId != $agentToSkip) {
                    continue;
                }
                $result[$agentId] = $userList[$agentId];
            }
            return $unassigned + $result;
        } catch (NoSuchEntityException $e) {
        }
        return [];
    }

    /**
     * Get agent name by id
     *
     * @param string $agentId
     * @return string
     */
    public function getOptionLabelByValue($agentId)
    {
        $agents = $this->getOptionArray();
        $label = __('Unassigned');
        if (array_key_exists($agentId, $agents)) {
            $label = $agents[$agentId];
        }
        return $label;
    }

    /**
     * Get available agents
     * @return array
     */
    public function getAvailableAgents()
    {
        $availableAgents = $this->configHelper->getAgents();
        if (!$availableAgents) {
            $availableAgents = [];
        } else {
            $availableAgents = explode(',', $availableAgents);
        }
        return $availableAgents;
    }

    /**
     * Adding "Inactive" label to the name for inactive user
     *
     * @param mixed $userCollection
     */
    protected function addInactiveLabelToUserName($userCollection)
    {
        $inactiveLabel = self::INACTIVE_LABEL;
        foreach ($userCollection->getItems() as $user) {
            if (!$user->getIsActive()) {
                $user->setUserFullname($user->getUserFullname() . ' ' . __($inactiveLabel));
            }
        }
    }
}
