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
 * @package   mirasvit/module-seo
 * @version   2.0.24
 * @copyright Copyright (C) 2017 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Seo\Ui\CanonicalRewrite\Form;

use Magento\Ui\DataProvider\AbstractDataProvider;
use Mirasvit\Seo\Api\Repository\CanonicalRewriteRepositoryInterface;
use Mirasvit\Seo\Api\Data\CanonicalRewriteStoreInterface;

class DataProvider extends AbstractDataProvider
{
    public function __construct(
        CanonicalRewriteRepositoryInterface $canonicalRewriteRepository,
        $name,
        $primaryFieldName,
        $requestFieldName,
        array $meta = [],
        array $data = []
    ) {
        $this->canonicalRewriteRepository = $canonicalRewriteRepository;
        $this->collection = $this->canonicalRewriteRepository->getCollection()->addStoreColumn();
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }


    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        $storeIds = [];
        if ($data = $this->collection->getData()) { //prepare store_id for multistore
            foreach ($data as $value) {
                $storeIds[$value[CanonicalRewriteStoreInterface::CANONICAL_REWRITE_ID]]
                    = $value[CanonicalRewriteStoreInterface::STORE_ID];
            }
        }

        $result = [];
        foreach ($this->collection->getItems() as $item) {
            if (isset($storeIds[$item->getId()])) {  //prepare store_id for multistore
                $item->setData( CanonicalRewriteStoreInterface::STORE_ID, $storeIds[$item->getId()]);
            }
            $result[$item->getId()] = $item->getData();
        }

        return $result;
    }
}
