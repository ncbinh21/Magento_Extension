<?php

namespace Forix\Catalog\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Config\Model\Config\Source\Yesno;

class Advanced implements ObserverInterface
{

	protected $_yesNo;

	public function __construct(Yesno $yesno)
	{
		$this->_yesNo = $yesno;
	}

	public function execute(Observer $observer)
	{
		/** @var $form \Magento\Framework\Data\Form */
		$form = $observer->getForm();
		$yesno = $this->_yesNo->toOptionArray();
		$fieldset = $form->getElement('advanced_fieldset');
		$fieldset->addField(
			'is_fitment',
			'select',
			[
				'name'   => 'is_fitment',
				'label'  => __('Is fitment'),
				'title'  => __('Is fitment'),
				'values' => $yesno
			]
		);
		return $form;
	}

}