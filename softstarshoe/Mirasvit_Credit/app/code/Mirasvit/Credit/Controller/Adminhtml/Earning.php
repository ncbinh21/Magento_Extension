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
 * @package   mirasvit/module-credit
 * @version   1.0.41
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Credit\Controller\Adminhtml;

use Magento\Ui\Model\Export\ConvertToCsv;
use Magento\Framework\App\Response\Http\FileFactory;

abstract class Earning extends \Magento\Backend\App\Action
{
    /**
     * @var \Mirasvit\Credit\Model\EarningFactory
     */
    protected $earningFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Backend\App\Action\Context
     */
    protected $context;

    /**
     * @var \Magento\Backend\Model\Session
     */
    protected $backendSession;

    /**
     * @var \Magento\Framework\Controller\ResultFactory
     */
    protected $resultFactory;

    /**
     * @var FileFactory
     */
    protected $fileFactory;

    /**
     * @param \Mirasvit\Credit\Model\EarningFactory $earningFactory
     * @param \Magento\Framework\Registry           $registry
     * @param FileFactory                           $fileRepository
     * @param \Magento\Backend\App\Action\Context   $context
     */
    public function __construct(
        \Mirasvit\Credit\Model\EarningFactory $earningFactory,
        \Magento\Framework\Registry $registry,
        FileFactory $fileRepository,
        \Magento\Backend\App\Action\Context $context
    ) {
        $this->earningFactory = $earningFactory;
        $this->registry = $registry;
        $this->fileFactory = $fileRepository;

        $this->context = $context;
        $this->backendSession = $context->getSession();
        $this->resultFactory = $context->getResultFactory();

        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     * @param \Magento\Backend\Model\View\Result\Page\Interceptor $resultPage
     * @return \Magento\Backend\Model\View\Result\Page\Interceptor
     */
    protected function initPage($resultPage)
    {
        $resultPage->setActiveMenu('Mirasvit_Credit::credit_transaction');
        $resultPage->getConfig()->getTitle()->prepend(__('Store Credit'));
        $resultPage->getConfig()->getTitle()->prepend(__('Earning Rules'));

        return $resultPage;
    }

    /**
     * @return \Mirasvit\Credit\Model\Transaction
     */
    public function initModel()
    {
        $transaction = $this->earningFactory->create();
        if ($this->getRequest()->getParam('id')) {
            $transaction->load($this->getRequest()->getParam('id'));
        }

        $this->registry->register('current_earning', $transaction);

        return $transaction;
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->context->getAuthorization()->isAllowed('Mirasvit_Credit::credit_earning');
    }
}
