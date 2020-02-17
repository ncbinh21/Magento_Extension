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



namespace Mirasvit\SeoSitemap\Observer;

use Magento\Framework\Event\ObserverInterface;

class CheckCronStatusObserver implements ObserverInterface
{
    /**
     * @var \Mirasvit\Core\Helper\Cron
     */
    protected $cronHelper;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;

    /**
     * @var \Magento\Backend\Model\Session
     */
    protected $backendSession;


    /**
     * @param \Mirasvit\Core\Helper\Cron       $mstcoreCron
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Magento\Backend\Model\Session      $backendSession
     */
    public function __construct(
        \Mirasvit\Core\Helper\Cron $mstcoreCron,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Backend\Model\Session $backendSession
    ) {
        $this->cronHelper = $mstcoreCron;
        $this->request = $request;
        $this->backendSession = $backendSession;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if ($this->request
            && $this->request->getParam('section') == 'seositemap') {
            $cronStatus = $this->cronHelper->checkCronStatus(
                false,
                false,
                'Cron job is required for sitemap automatical generate. Automatical generate can be configured in
                System->Configuration->Catalog->Google Sitemap->Generation Settings. Cron for magento is not running.
                 To setup a cron job follow the link.'
            );
            if ($cronStatus !== true) {
                $this->backendSession->addError($cronStatus);
            }
        };
    }
}
