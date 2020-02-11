<?php
/**
 * Created by: Bruce
 * Date: 2/4/16
 * Time: 10:35
 */
namespace Forix\ReCaptcha\Model\System\Source;

class Forms extends \Magento\Framework\App\Config\Value{
	public function toOptionArray() {
		$optionArray = [];
		$backendConfig = $this->_config->getValue("recaptcha/setting/forms_area", 'default');
		if ($backendConfig) {
			foreach ($backendConfig as $formName => $formConfig) {
				if (!empty($formConfig['label'])) {
					$optionArray[] = ['label' => $formConfig['label'], 'value' => $formName];
				}
			}
		}
		return $optionArray;
	}
}