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

use Mirasvit\Seo\Api\Data\CanonicalRewriteInterface;
use Mirasvit\Seo\Controller\Adminhtml\CanonicalRewrite;
use Magento\Ui\Component\MassAction\Filter;
use Mirasvit\Seo\Api\Repository\CanonicalRewriteRepositoryInterface;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Mirasvit\Seo\Api\Service\CompatibilityServiceInterface;

class Delete extends CanonicalRewrite
{
    /**
     * @var Filter
     */
    private $filter;

    public function __construct(
        CompatibilityServiceInterface $compatibilityService,
        Filter $filter,
        Registry $registry,
        CanonicalRewriteRepositoryInterface $canonicalRewriteRepository,
        Context $context
    ) {
        $this->compatibilityService = $compatibilityService;
        $this->filter = $filter;
        $this->canonicalRewriteRepository = $canonicalRewriteRepository;

        parent::__construct($compatibilityService, $registry, $canonicalRewriteRepository, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $ids = [];

        if ($this->getRequest()->getParam(CanonicalRewriteInterface::ID_ALIAS)) {
            $ids = [$this->getRequest()->getParam(CanonicalRewriteInterface::ID_ALIAS)];
        }

        if ($this->getRequest()->getParam(Filter::SELECTED_PARAM)
            || $this->getRequest()->getParam(Filter::EXCLUDED_PARAM)
        ) {
            $ids = $this->filter->getCollection($this->canonicalRewriteRepository->getCollection())->getAllIds();
        }

        if ($ids) {
            foreach ($ids as $id) {
                try {
                    $model = $this->canonicalRewriteRepository->get($id);
                    $this->canonicalRewriteRepository->delete($model);
                } catch (\Exception $e) {
                    $this->messageManager->addErrorMessage($e->getMessage());
                }
            }

            $this->messageManager->addSuccessMessage(
                __('%1 item(s) was removed', count($ids))
            );
        } else {
            $this->messageManager->addErrorMessage(__('Please select item(s)'));
        }

        return $this->resultRedirectFactory->create()->setPath('*/*/');
    }
}
