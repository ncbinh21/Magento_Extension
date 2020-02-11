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



namespace Mirasvit\Seo\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Mirasvit\Seo\Api\Data\CanonicalRewriteInterface;

class CanonicalRewrite extends AbstractDb
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(CanonicalRewriteInterface::TABLE_NAME, CanonicalRewriteInterface::ID);
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    //    public function _afterSave(\Magento\Framework\Model\AbstractModel $object)
    //    {
    //        if (!$object->getIsMassStatus()) {
    //            $this->_saveRule($object);
    ////            $this->saveStore($object);
    //        }
    //
    //        return parent::_afterSave($object);
    //    }
    //
    //    /**
    //     * @param \Magento\Framework\Model\AbstractModel $object
    //     *
    //     * @return void
    //     */
    //    protected function _saveRule(\Magento\Framework\Model\AbstractModel $object)
    //    {
    //        if ($object->getData('rule') && is_array($object->getData('rule'))) {
    //            $ruleData = $object->getData('rule');
    //            $model = $object->getRule();
    //            $model->setCanonicalRewriteId($object->getId())
    //                ->loadPost($ruleData)
    //                ->save();
    //        }
    //    }
    //
    //    /**
    //     * @param bool|false $ruleId
    //     * @return \Mirasvit\Seo\Model\Template
    //     */
    //    public function getRule($ruleId = false)
    //    {
    //        $ruleId = ($ruleId) ? $ruleId : $this->getId();
    //
    //        $rule = $this->templateCollectionFactory->create()
    //            ->addFieldToFilter('template_id', $ruleId)
    //            ->getFirstItem();
    //        $rule = $rule->load($rule->getId());
    //
    //        return $rule;
    //    }
}
