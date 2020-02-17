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



namespace Mirasvit\Seo\Block\Adminhtml\System\Template;

class Run extends \Magento\Backend\Block\AbstractBlock
{
    /**
     * @var \Mirasvit\Seo\Model\System\Template\Worker
     */
    protected $systemTemplateWorker;

    /**
     * @var \Magento\Backend\Block\Context
     */
    protected $context;

    /**
     * @param \Mirasvit\Seo\Model\System\Template\Worker $systemTemplateWorker
     * @param \Magento\Backend\Block\Context             $context
     * @param array                                      $data
     */
    public function __construct(
        \Mirasvit\Seo\Model\System\Template\Worker $systemTemplateWorker,
        \Magento\Backend\Block\Context $context,
        array $data = []
    ) {
        $this->systemTemplateWorker = $systemTemplateWorker;
        $this->context = $context;
        parent::__construct($context, $data);
    }

    /**
     * @return \Mirasvit\Seo\Model\System\Template\Worker
     */
    public function getWorker()
    {
        return $this->systemTemplateWorker;
    }
}
