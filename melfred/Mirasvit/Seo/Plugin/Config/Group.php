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



namespace Mirasvit\Seo\Plugin\Config;

class Group
{
    /**
     * @var \Mirasvit\Seo\Api\Data\BlogMxInterface
     */
    protected $blogMx;

    public function __construct(
        \Mirasvit\Seo\Api\Config\BlogMxInterface $blogMx
    ) {
        $this->blogMx = $blogMx;
    }

    /**
     * Check whether group should be expanded
     *
     * @param Magento\Config\Model\Config\Structure\Element\Group\Interceptor $subject
     * @return void
     */
    public function beforeIsExpanded($subject)
    {
        $data = $subject->getData();

        if ($data['id'] == 'blogmx_snippets'
            && !$this->blogMx->isEnabled()) {
                $data['fieldset_css'] = 'seo__hide_group';
                $subject->setData($data, 'default');
        }
    }
}
