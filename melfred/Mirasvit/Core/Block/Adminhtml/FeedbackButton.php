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
 * @package   mirasvit/module-core
 * @version   1.2.64
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Core\Block\Adminhtml;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Mirasvit\Core\Model\Config;

class FeedbackButton extends Template
{
    private $config;

    public function __construct(
        Config $config,
        Context $context
    ) {
        $this->config = $config;

        parent::__construct($context);
    }

    /**
     * @return bool
     */
    public function isVisible()
    {
        if (strpos($this->getRequest()->getControllerModule(), 'Mirasvit_') === false) {
            return false;
        }

        if ($this->config->isFeedbackActive() == false) {
            return false;
        }

        return true;
    }

    /**
     * @return string
     */
    public function getActionUrl()
    {
        return $this->config->getFeedbackUrl();
    }
}
