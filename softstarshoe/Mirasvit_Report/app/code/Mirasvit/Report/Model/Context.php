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
 * @package   mirasvit/module-report
 * @version   1.2.27
 * @copyright Copyright (C) 2017 Mirasvit (https://mirasvit.com/)
 */


namespace Mirasvit\Report\Model;

use Mirasvit\Report\Api\Data\CollectionInterface;
use Mirasvit\Report\Model\CollectionFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Backend\Model\Url as BackendUrl;
use Mirasvit\Report\Api\Repository\MapRepositoryInterface;

class Context
{
    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var Collection
     */
    protected $collection;

    /**
     * @var MapRepositoryInterface
     */
    protected $mapRepository;

    public function __construct(
        MapRepositoryInterface $mapRepository,
        CollectionFactory $collectionFactory,
        RequestInterface $request,
        BackendUrl $urlManager
    ) {
        $this->mapRepository = $mapRepository;
        $this->collectionFactory = $collectionFactory;
        $this->request = $request;
        $this->urlManager = $urlManager;

        $this->initCollection();
    }

    /**
     * @return $this
     */
    public function initCollection()
    {
        $this->collection = $this->collectionFactory->create();

        return $this;
    }

    /**
     * @return CollectionInterface
     */
    public function getCollection()
    {
        return $this->collection;
    }

    /**
     * @return MapRepositoryInterface
     */
    public function getMapRepository()
    {
        return $this->mapRepository;
    }

    /**
     * @return RequestInterface
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @return BackendUrl
     */
    public function getUrlManager()
    {
        return $this->urlManager;
    }
}
