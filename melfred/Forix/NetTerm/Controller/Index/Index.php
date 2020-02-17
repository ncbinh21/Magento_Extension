<?php
/**
 * Created by Mercurial
 * Job Title: Magento Developer
 */
namespace  Forix\NetTerm\Controller\Index;

class Index extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * Index constructor.
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Customer\Model\Session $session
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\Session $session,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->session = $session;
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Redirect|\Magento\Framework\Controller\ResultInterface|\Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        if ($this->session->isLoggedIn()) {
            $resultPage = $this->resultPageFactory->create();
            $resultPage->getConfig()->getTitle()->set(__('Apply For Net Terms'));
            return $resultPage;
        }
        $redirectUrl = $this->resultRedirectFactory->create();
        $resultRedirect = $redirectUrl->setPath('customer/account/login');
        return $resultRedirect;
    }
}
