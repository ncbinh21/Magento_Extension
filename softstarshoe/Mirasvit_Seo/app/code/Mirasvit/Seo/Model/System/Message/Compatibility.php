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



namespace Mirasvit\Seo\Model\System\Message;

use Magento\Framework\Notification\MessageInterface;
use Magento\Framework\Module\Manager;
use Mirasvit\Core\Model\ModuleFactory;

class Compatibility implements MessageInterface
{
    /**
     * @param Manager $moduleManager
     * @param ModuleFactory $moduleFactory
     */
    public function __construct(
        Manager $moduleManager,
        ModuleFactory $moduleFactory
    ) {
        $this->moduleManager = $moduleManager;
        $this->moduleFactory = $moduleFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentity()
    {
        return 'm__fpc_warmer_compatibility';
    }

    /**
     * {@inheritdoc}
     */
    public function isDisplayed()
    {
        $moduleName = 'Mirasvit_CacheWarmer';
        if ($this->moduleManager->isEnabled($moduleName)) {
            $composerInformation = $this->moduleFactory->create()->getComposerInformation($moduleName);
            $installedVersion = $composerInformation['version'];
            
            if ($installedVersion && version_compare($installedVersion, '1.0.33', '<')) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getText()
    {
        return 'For full compatibility with current Mirasvit SEO'
            . ' version please update Mirasvit CacheWarmer to version 1.0.33 or higher';
    }

    /**
     * {@inheritdoc}
     */
    public function getSeverity()
    {
        return self::SEVERITY_MAJOR;
    }
}
