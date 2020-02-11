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
 * @package   mirasvit/module-email-report
 * @version   1.0.5
 * @copyright Copyright (C) 2017 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\EmailReport\Model;


use Magento\Framework\Model\AbstractModel;
use Mirasvit\EmailReport\Api\Data\ClickInterface;
use Mirasvit\EmailReport\Model\ReportProperties;

class Click extends AbstractModel implements ClickInterface
{
    use ReportProperties;

    /**
     * {@inheritDoc}
     */
    protected function _construct()
    {
        $this->_init('\Mirasvit\EmailReport\Model\ResourceModel\Click');
    }
}
