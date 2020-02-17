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
 * @version   2.0.64
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Seo\Controller\Adminhtml\CanonicalRewrite;

use Mirasvit\Seo\Controller\Adminhtml\CanonicalRewrite;
use Mirasvit\Seo\Api\Data\CanonicalRewriteInterface;

class NewConditionHtml extends CanonicalRewrite
{
    /**
     * @return void
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam(CanonicalRewriteInterface::ID_ALIAS);
        $typeArr = explode('|', str_replace('-', '/', $this->getRequest()->getParam('type')));
        $type = $typeArr[0];

        $model = $this->context->getObjectManager()->create($type)
            ->setId($id)
            ->setType($type)
            ->setRule($this->canonicalRewriteRepository->create())
            ->setPrefix('conditions')
            ->setFormName($this->getRequest()->getParam('ruleform'));

        if (!empty($typeArr[1])) {
            $model->setAttribute($typeArr[1]);
        }

        if ($model instanceof \Magento\Rule\Model\Condition\AbstractCondition) {
            $model->setJsFormObject($this->getRequest()->getParam('form'));
            $html = $model->asHtmlRecursive();
        } else {
            $html = '';
        }
        $this->getResponse()->setBody($html);
    }
}
