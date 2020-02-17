<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Forix\ProductWizard\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\UrlRewrite\Model\UrlPersistInterface;
use Magento\Framework\Event\ObserverInterface;
use Forix\ProductWizard\Model\WizardUrlRewriteGenerator;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;

class ProcessUrlRewriteSavingObserver implements ObserverInterface
{
    /**
     * @var \Forix\ProductWizard\Model\WizardUrlRewriteGenerator
     */
    protected $wizardUrlRewriteGenerator;

    /**
     * @var UrlPersistInterface
     */
    protected $urlPersist;

    /**
     * @param \Forix\ProductWizard\Model\WizardUrlRewriteGenerator $cmsPageUrlRewriteGenerator
     * @param UrlPersistInterface $urlPersist
     */
    public function __construct(WizardUrlRewriteGenerator $wizardUrlRewriteGenerator, UrlPersistInterface $urlPersist)
    {
        $this->wizardUrlRewriteGenerator = $wizardUrlRewriteGenerator;
        $this->urlPersist = $urlPersist;
    }

    /**
     * Generate urls for UrlRewrite and save it in storage
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(EventObserver $observer)
    {
        /** @var $wizard \Forix\ProductWizard\Model\Wizard */
        $wizard = $observer->getEvent()->getObject();

        if ($wizard->dataHasChangedFor('identifier') || $wizard->dataHasChangedFor('store_id')) {
            $urls = $this->wizardUrlRewriteGenerator->generate($wizard);

            $this->urlPersist->deleteByData([
                UrlRewrite::ENTITY_ID => $wizard->getId(),
                UrlRewrite::ENTITY_TYPE => WizardUrlRewriteGenerator::ENTITY_TYPE,
            ]);
            $this->urlPersist->replace($urls);
        }
    }
}
