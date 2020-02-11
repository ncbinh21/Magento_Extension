<?php


namespace Forix\FanPhoto\Block\Register;

class Form extends \Magento\Framework\View\Element\Template
{
	/**
	 * Construct
	 *
	 * @param \Magento\Framework\View\Element\Template\Context $context
	 * @param array $data
	 */
	public function __construct(
		\Magento\Backend\Block\Template\Context $context,
		array $data = []
	)
	{
		parent::__construct($context, $data);
	}

	/**
	 * Retrieve form action
	 *
	 * @return string
	 */
	public function getFormAction()
	{
		// companymodule is given in routes.xml
		// controller_name is folder name inside controller folder
		// action is php file name inside above controller_name folder

		return '/fanphoto/register/action';
		// here controller_name is manage, action is contact
	}
}
