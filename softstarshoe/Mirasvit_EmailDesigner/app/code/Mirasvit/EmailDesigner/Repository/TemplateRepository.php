<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-email-designer
 * @version   1.0.16
 * @copyright Copyright (C) 2017 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\EmailDesigner\Repository;


use Magento\Framework\EntityManager\EntityManager;
use Mirasvit\EmailDesigner\Api\Data\TemplateInterface;
use Mirasvit\EmailDesigner\Api\Data\TemplateInterfaceFactory;
use Mirasvit\EmailDesigner\Api\Repository\TemplateRepositoryInterface;
use Mirasvit\EmailDesigner\Model\ResourceModel\Template\CollectionFactory;

class TemplateRepository implements TemplateRepositoryInterface
{
    /**
     * @var TemplateInterface[]
     */
    private $templateRegistry = [];

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var TemplateInterfaceFactory
     */
    private $modelFactory;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * TemplateRepository constructor.
     *
     * @param EntityManager            $entityManager
     * @param TemplateInterfaceFactory $modelFactory
     * @param CollectionFactory        $collectionFactory
     */
    public function __construct(
        EntityManager $entityManager,
        TemplateInterfaceFactory $modelFactory,
        CollectionFactory $collectionFactory
    ) {
        $this->entityManager = $entityManager;
        $this->modelFactory = $modelFactory;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getCollection()
    {
        return $this->collectionFactory->create();
    }

    /**
     * {@inheritdoc}
     */
    public function create()
    {
        return $this->modelFactory->create();
    }
}
