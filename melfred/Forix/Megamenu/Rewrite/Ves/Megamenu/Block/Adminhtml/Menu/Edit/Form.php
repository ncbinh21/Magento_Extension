<?php
/**
 * Created by Nick Howard.
 * Job Title: Magento Developer
 * Project: html
 */

namespace Forix\Megamenu\Rewrite\Ves\Megamenu\Block\Adminhtml\Menu\Edit;


class Form extends \Ves\Megamenu\Block\Adminhtml\Menu\Edit\Form
{
	protected function _prepareForm()
	{
		$result = parent::_prepareForm();
		$form = $this->getForm();
		$fieldset = $form->getElements()->searchById('base_fieldset');
		if(!is_null($fieldset)){
			$fieldset->removeField('customer_group_ids');
		}

		return $result;
	}

}