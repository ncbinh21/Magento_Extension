<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */

namespace Amasty\Shopby\Model;

class Request extends \Magento\Framework\DataObject
{
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    private $request;

    /**
     * @var array
     */
    private $brandParam;

    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        array $data = []
    ) {
        $this->request = $request;
        parent::__construct($data);
    }

    /**
     * @param \Magento\Catalog\Model\Layer\Filter\AbstractFilter $filter
     * @return mixed|string
     */
    public function getFilterParam(\Magento\Catalog\Model\Layer\Filter\AbstractFilter $filter)
    {
        return $this->getParam($filter->getRequestVar());
    }

    /**
     * @param $brandParam
     * @return $this
     */
    public function setBrandParam($brandParam)
    {
        $this->brandParam = $brandParam;
        return $this;
    }

    /**
     * @return array
     */
    public function getBrandParam()
    {
        return $this->brandParam;
    }

    /**
     * @param $requestVar
     * @return mixed
     */
    public function getParam($requestVar)
    {
        $bulkParams = $this->getBulkParams();
        if (array_key_exists($requestVar, $bulkParams)) {
            $data = implode(',', $bulkParams[$requestVar]);
        } else {
            $data = $this->request->getParam($requestVar);
        }

        return $data;
    }

    /**
     * @return array
     */
    protected function getBulkParams()
    {
        $bulkParams = $this->request->getParam('amshopby', []);
        $brandParam = $this->getBrandParam();
        if ($brandParam) {
            $bulkParams[$brandParam['code']] = $brandParam['value'];
        }
        return $bulkParams;
    }
}
