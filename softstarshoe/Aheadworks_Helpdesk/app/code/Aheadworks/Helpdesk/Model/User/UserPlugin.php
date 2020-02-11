<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Helpdesk\Model\User;

/**
 * Class UserPlugin
 * @package Aheadworks\Helpdesk\Model\User
 */
class UserPlugin
{
    /**
     * Bookmark helper
     *
     * @var \Aheadworks\Helpdesk\Helper\Bookmark
     */
    protected $definedBookmarkHelper;

    /**
     * Bookmark repository
     *
     * @var \Magento\Ui\Api\BookmarkRepositoryInterface
     */
    protected $bookmarkRepository;

    /**
     * Ticket collection factory
     * @var \Aheadworks\Helpdesk\Model\ResourceModel\Ticket\CollectionFactory
     */
    protected $ticketCollection;

    /**
     * Ticket repository
     * @var \Aheadworks\Helpdesk\Api\TicketRepositoryInterface
     */
    protected $ticketRepository;

    /**
     * Ticket flat repository
     * @var \Aheadworks\Helpdesk\Api\TicketFlatRepositoryInterface
     */
    protected $ticketFlatRepository;

    /**
     * Resource config
     * @var \Magento\Config\Model\ResourceModel\Config
     */
    protected $resourceConfig;

    /**
     * Agents source
     *
     * @var \Aheadworks\Helpdesk\Model\Source\Ticket\Agent
     */
    protected $agentSource;

    /**
     * Cache types list
     *
     * @var \Magento\Framework\App\Cache\TypeList
     */
    protected $cacheTypeList;

    /**
     * Constructor
     *
     * @param \Magento\Ui\Api\BookmarkRepositoryInterface $bookmarkRepository
     * @param \Aheadworks\Helpdesk\Helper\Bookmark $bookmarkHelper
     * @param \Aheadworks\Helpdesk\Model\ResourceModel\Ticket\CollectionFactory $ticketCollection
     * @param \Aheadworks\Helpdesk\Api\TicketRepositoryInterface $ticketRepository
     * @param \Aheadworks\Helpdesk\Api\TicketFlatRepositoryInterface $ticketFlatRepository
     * @param \Magento\Config\Model\ResourceModel\Config $resourceConfig
     * @param \Aheadworks\Helpdesk\Model\Source\Ticket\Agent $agentSource
     * @param \Magento\Framework\App\Cache\TypeList $cacheTypeList
     */
    public function __construct(
        \Magento\Ui\Api\BookmarkRepositoryInterface $bookmarkRepository,
        \Aheadworks\Helpdesk\Helper\Bookmark $bookmarkHelper,
        \Aheadworks\Helpdesk\Model\ResourceModel\Ticket\CollectionFactory $ticketCollection,
        \Aheadworks\Helpdesk\Api\TicketRepositoryInterface $ticketRepository,
        \Aheadworks\Helpdesk\Api\TicketFlatRepositoryInterface $ticketFlatRepository,
        \Magento\Config\Model\ResourceModel\Config $resourceConfig,
        \Aheadworks\Helpdesk\Model\Source\Ticket\Agent $agentSource,
        \Magento\Framework\App\Cache\TypeList $cacheTypeList
    ) {
        $this->definedBookmarkHelper = $bookmarkHelper;
        $this->bookmarkRepository = $bookmarkRepository;
        $this->ticketCollection = $ticketCollection;
        $this->ticketRepository = $ticketRepository;
        $this->ticketFlatRepository = $ticketFlatRepository;
        $this->resourceConfig = $resourceConfig;
        $this->agentSource = $agentSource;
        $this->cacheTypeList = $cacheTypeList;
    }

    /**
     * Run plugin after save user
     *
     * @param \Magento\User\Model\User $subject
     * @param callable $proceed
     */
    public function aroundAfterSave(
        \Magento\User\Model\User $subject,
        \Closure $proceed
    ) {
        $proceed();
        if ($subject->isObjectNew()) {
            $this->definedBookmarkHelper->proceedAll($subject);
        }
        if (!$subject->getIsActive()) {
            $this->_removeFromAvailableAgents($subject->getId());
        }
    }

    /**
     * Run plugin after delete user
     *
     * @param \Magento\User\Model\User $subject
     * @param $result
     * @return mixed
     */
    public function afterDelete(
        \Magento\User\Model\User $subject,
        $result
    ) {
        $userId = $subject->getUserId();

        $ticketCollection = $this->ticketCollection->create()->addFilter('agent_id', $userId, 'public')->load();
        foreach ($ticketCollection as $ticket) {
            try {
                $ticketModel = $this->ticketRepository->getById($ticket->getId());
                $ticketModel->setAgentId(0);
                $this->ticketRepository->save($ticketModel);
                $ticketFlatModel = $this->ticketFlatRepository->getByTicketId($ticket->getId());
                $ticketFlatModel->setAgentId(0);
                $ticketFlatModel->setAgentName(__('Unassigned'));
                $this->ticketFlatRepository->save($ticketFlatModel);
            } catch (\Exception $e) {
                continue;
            }
        }
        return $result;
    }

    /**
     * Remove from available agents
     *
     * @param int $agentId
     */
    private function _removeFromAvailableAgents($agentId)
    {
        $availableAgents = $this->agentSource->getAvailableAgents();
        if (($key = array_search($agentId, $availableAgents)) !== false) {
            unset($availableAgents[$key]);
        }
        $availableAgents = implode(',', $availableAgents);
        $this->_saveAvailableAgents($availableAgents);
        $this->cacheTypeList->invalidate(\Magento\Framework\App\Cache\Type\Config::TYPE_IDENTIFIER);
    }

    /**
     * Save available agents
     *
     * @param string $availableAgents
     */
    private function _saveAvailableAgents($availableAgents)
    {
        $this->resourceConfig->saveConfig(
            \Aheadworks\Helpdesk\Helper\Config::XML_PATH_AGENTS_USER,
            $availableAgents,
            'default',
            0
        );
    }
}
