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



namespace Mirasvit\Seo\Observer\System\Cms;

use Magento\Framework\Event\ObserverInterface;

class SavePageObserver implements ObserverInterface
{
    /**
     * @var \Magento\Backend\Model\Session
     */
    protected $backendSession;

    /**
     * @param \Magento\Backend\Model\Session $backendSession
     */
    public function __construct(
        \Magento\Backend\Model\Session $backendSession
    ) {
        $this->backendSession = $backendSession;
    }

    /**
     * @param string $observer
     *
     * @return void
     */
    public function savePage($observer)
    {
        $model = $observer->getEvent()->getPage();
        $request = $observer->getEvent()->getRequest();
        $data = $request->getPost();
        if ($data['alternate_group']) {
            $model->setAlternateGroup($data['alternate_group']);
        }
        try {
            $model->save();
        } catch (\Exception $e) {
            $this->backendSession->addError($e->getMessage());
            $this->backendSession->setFormData($data);

            return;
        }
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
        $this->savePage($observer);
    }
}
