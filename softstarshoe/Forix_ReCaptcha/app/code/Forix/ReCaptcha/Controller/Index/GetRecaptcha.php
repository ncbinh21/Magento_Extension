<?php
/**
 * Created by: Bruce
 * Date: 2/3/16
 * Time: 15:35
 */
namespace Forix\ReCaptcha\Controller\Index;

use Magento\Framework\App\Action\Context;

class GetRecaptcha extends \Magento\Framework\App\Action\Action {

	/** @var  \Magento\Framework\View\Result\Page */
	protected $resultPageFactory;


	/** @param \Magento\Framework\App\Action\Context $context */

	public function __construct(Context $context) {
		parent::__construct($context);
	}

	public function execute() {
		$formId = $this->getRequest()->getParam("form_id");
		$layout = $this->_view->loadLayout()->getLayout();
		$block = $layout->getBlock("recaptcha")->setFormId($formId);
		$jsonEncoder = $this->_objectManager->create("Magento\Framework\Json\Encoder");
		$this->getResponse()->setBody($jsonEncoder->encode(["form_id" => $formId, 'recaptcha' => $block->toHtml()]));
	}

}