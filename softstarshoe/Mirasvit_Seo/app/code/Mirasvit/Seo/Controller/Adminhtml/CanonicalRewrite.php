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



namespace Mirasvit\Seo\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Mirasvit\Seo\Api\Repository\CanonicalRewriteRepositoryInterface;
use Mirasvit\Seo\Api\Data\CanonicalRewriteInterface;
use Magento\Framework\Registry;
use Mirasvit\Seo\Api\Service\CompatibilityServiceInterface;

abstract class CanonicalRewrite extends Action
{
    /**
     * @param CompatibilityServiceInterface $compatibilityService
     * @param Registry $registry
     * @param CanonicalRewriteRepositoryInterface $canonicalRewriteRepository
     * @param Context $context
     */
    public function __construct(
        CompatibilityServiceInterface $compatibilityService,
        Registry $registry,
        CanonicalRewriteRepositoryInterface $canonicalRewriteRepository,
        Context $context
    ) {
        $this->compatibilityService = $compatibilityService;
        $this->registry = $registry;
        $this->canonicalRewriteRepository = $canonicalRewriteRepository;
        $this->context = $context;

        parent::__construct($context);
    }

    /**
     * @param \Magento\Backend\Model\View\Result\Page $resultPage
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected function initPage($resultPage)
    {
        $resultPage->setActiveMenu('Mirasvit_Seo::seo');
        $resultPage->getConfig()->getTitle()->prepend(__('SEO'));
        $resultPage->getConfig()->getTitle()->prepend(__('Canonical Rewrite'));

        return $resultPage;
    }

    /**
     * @return CanonicalRewriteInterface
     */
    public function initModel()
    {
        $model = $this->canonicalRewriteRepository->create();

        if ($this->getRequest()->getParam(CanonicalRewriteInterface::ID_ALIAS)) {
            $model = $this->canonicalRewriteRepository
                ->get($this->getRequest()->getParam(CanonicalRewriteInterface::ID_ALIAS));
        }

        $this->registry->register(CanonicalRewriteInterface::MODEL, $model);

        return $model;
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->context->getAuthorization()->isAllowed('Mirasvit_Seo::seo_canonicalRewrite');
    }
}
