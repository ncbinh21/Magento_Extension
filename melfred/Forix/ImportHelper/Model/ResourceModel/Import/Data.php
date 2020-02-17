<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Forix\ImportHelper\Model\ResourceModel\Import;
/**
 * ImportExport import data resource model
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Data extends  \Magento\ImportExport\Model\ResourceModel\Import\Data
{
    /**
     * Strip inline translations from text
     *
     * @param array|string &$body
     * @return $this
     */
    protected function stripInlineTranslations(&$body)
    {
        if (is_array($body)) {
            foreach ($body as &$part) {
                $this->stripInlineTranslations($part);
            }
        } else {
            if (is_string($body)) {
                $body = utf8_encode($body);
            }
        }
        return $this;
    }


    /**
     * Save import rows bunch.
     *
     * @param string $entity
     * @param string $behavior
     * @param array $data
     * @param int $page
     * @param string $filename
     * @return int
     */
    public function saveBunch($entity, $behavior, array $data)
    {
        $data = json_encode($data);
        if(false === $data){
            $this->stripInlineTranslations($data);
            $data = json_encode($data);
        }
        return $this->getConnection()->insert(
            $this->getMainTable(),
            ['behavior' => $behavior, 'entity' => $entity, 'data' => $data]
        );
    }

    /**
     * Clean all bunches from table.
     *
     * @return \Magento\Framework\DB\Adapter\AdapterInterface
     */
    public function cleanBunches()
    {
        return $this->getConnection()->delete($this->getMainTable(), ['entity' => $this->getEntityTypeCode()]);
    }

}