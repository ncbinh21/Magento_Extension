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



namespace Mirasvit\EmailReport\Controller\Adminhtml\Report;


use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Registry;
use Mirasvit\EmailReport\Reports\Statistic;
use Mirasvit\Report\Api\Repository\ReportRepositoryInterface;

class Index extends Action
{
    /**
     * Authorization level of a basic admin session.
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Mirasvit_EmailReport::emailreport_report';

    /**
     * @var ReportRepositoryInterface
     */
    private $reportRepository;
    /**
     * @var Registry
     */
    private $registry;
    /**
     * @var Action\Context
     */
    private $context;

    /**
     * Index constructor.
     *
     * @param Registry                  $registry
     * @param ReportRepositoryInterface $reportRepository
     * @param Action\Context            $context
     */
    public function __construct(
        Registry $registry,
        ReportRepositoryInterface $reportRepository,
        Action\Context $context
    ) {
        parent::__construct($context);
        $this->reportRepository = $reportRepository;
        $this->registry = $registry;
        $this->context = $context;
    }

    /**
     * @param \Magento\Backend\Model\View\Result\Page $resultPage
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected function initPage($resultPage)
    {
        $resultPage->setActiveMenu(self::ADMIN_RESOURCE);
        $resultPage->getConfig()->getTitle()->prepend(__('Follow Up Email'));
        $resultPage->getConfig()->getTitle()->prepend(__('Reports'));

        return $resultPage;
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $this->registry->register('current_report', $this->reportRepository->get(Statistic::ID));
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $this->initPage($resultPage);

        return $resultPage;
    }
}
