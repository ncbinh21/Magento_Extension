<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_OrderStatus
 */


namespace Amasty\OrderStatus\Model;

use Magento\Framework\Model\AbstractModel;

class Template extends AbstractModel
{
    protected function _construct()
    {
        $this->_init('Amasty\OrderStatus\Model\ResourceModel\Template');
    }

    public function saveTemplates($storeEmailTemplate, \Amasty\OrderStatus\Model\Status $status)
    {
        /** @var \Amasty\OrderStatus\Model\ResourceModel\Template $resource */
        $resource = $this->getResource();
        $resource->removeStatusTemplates($status->getId());
        if (!empty($storeEmailTemplate)) {
            foreach ($storeEmailTemplate as $storeId => $templateId) {
                $data = array(
                    'status_id' => $status->getId(),
                    'store_id' => $storeId,
                    'template_id' => $templateId
                );
                $this->isObjectNew(true);
                $this->setData($data);
                $this->save();
            }
        }
    }

    /**
     * Adds e-mail template IDs to the status model object
     *
     * @param \Amasty\OrderStatus\Model\Status $statusModel
     */
    public function attachTemplates(\Amasty\OrderStatus\Model\Status $status)
    {
        $collection = $this->getResourceCollection();
        $collection->addFieldToFilter('status_id', ['eq' => $status->getId()]);
        foreach ($collection as $template) {
            $key = 'store_template[' . $template->getStoreId() . ']';
            $status->setData($key, $template->getTemplateId());
        }
    }
}
