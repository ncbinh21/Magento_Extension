<?php

namespace Forix\Company\Model\Config\Source;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;

class RoleList extends AbstractSource
{
    /**
     * @var \Magento\Company\Model\ResourceModel\Role\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var array $options
     */
    protected $options;

    /**
     * RoleList constructor.
     * @param \Magento\Company\Model\ResourceModel\Role\CollectionFactory $collectionFactory
     */
    public function __construct(
        \Magento\Company\Model\ResourceModel\Role\CollectionFactory $collectionFactory
    ) {
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * Get Options
     *
     * @return array
     */
    public function getAllOptions()
    {
        if ($this->options == null) {
            $companyList = $this->collectionFactory->create();
            foreach ($companyList as $company) {
                $this->options[$company->getId()] = $company->getRoleName();
            }
        }
        return $this->options;
    }
}