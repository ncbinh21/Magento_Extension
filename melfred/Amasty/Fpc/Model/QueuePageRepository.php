<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Fpc
 */


namespace Amasty\Fpc\Model;

use Amasty\Fpc\Api\Data\QueuePageInterface;
use Amasty\Fpc\Api\QueuePageRepositoryInterface;

class QueuePageRepository implements QueuePageRepositoryInterface
{
    /**
     * @var ResourceModel\Queue\Page
     */
    private $pageResource;
    /**
     * @var Queue\PageFactory
     */
    private $pageFactory;

    public function __construct(
        ResourceModel\Queue\Page $pageResource,
        Queue\PageFactory $pageFactory
    ) {
        $this->pageResource = $pageResource;
        $this->pageFactory = $pageFactory;
    }

    public function delete(QueuePageInterface $entity)
    {
        $this->pageResource->delete($entity);
    }

    public function save(QueuePageInterface $entity)
    {
        $this->pageResource->save($entity);
    }

    public function addPage($pageData)
    {
        /** @var Queue\Page $page */
        $page = $this->pageFactory->create();

        $page->setData($pageData);

        $this->save($page);

        return $page;
    }

    public function clear()
    {
        $this->pageResource->truncate();
    }
}
