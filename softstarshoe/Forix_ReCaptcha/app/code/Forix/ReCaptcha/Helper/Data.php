<?php
/**
 * Created by: Bruce
 * Date: 2/2/16
 * Time: 16:55
 */

namespace Forix\ReCaptcha\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

class Data extends AbstractHelper {
	/* @var $_jsonHelper \Magento\Framework\Json\Helper\Data */
	protected $_jsonHelper;

	/**
	 * Data constructor.
	 * @param \Magento\Framework\Json\Helper\Data $jsonHelper
	 * @param \Magento\Framework\App\Helper\Context $context
	 */
	public function __construct(
		\Magento\Framework\Json\Helper\Data $jsonHelper,
		\Magento\Framework\App\Helper\Context $context) {
		$this->_jsonHelper = $jsonHelper;
		parent::__construct($context);
	}

	/**
	 * @return bool
	 */
	public function isEnabled() {
		return (bool)$this->_getConfigValue("recaptcha/setting/is_active");
	}

	/**
	 * @param $value
	 * @return mixed
	 */
	protected function _getConfigValue($value) {
		return $this->scopeConfig->getValue($value);
	}

	/**
	 * @return mixed
	 */
	public function getSiteKey() {
		return $this->_getConfigValue("recaptcha/setting/site_key");
	}

	/**
	 * @return string
	 */
	public function getTheme() {
		return "light";
	}

	/**
	 * @return mixed
	 */
	public function getSecretKey() {
		return $this->_getConfigValue("recaptcha/setting/secret_key");
	}

	/**
	 * @return array
	 */
	public function getFormApplied(){
		$forms = explode(",",$this->_getConfigValue("recaptcha/setting/forms"));
		return $forms;
	}

	/**
	 * @param $responseText
	 * @return mixed
	 */
	public function verifyCaptcha($responseText){
		$secret = $this->getSecretKey();
		$url = "https://www.google.com/recaptcha/api/siteverify";
		$data = array("secret"=>$secret, "response"=>$responseText);
		$url = $url . "?" . http_build_query($data);
		//$result = $this->_jsonHelper->jsonDecode(file_get_contents($url));
		//@:Chan recommended
		$arrContextOptions=array(
			"ssl"=>array(
				"verify_peer"=>false,
				"verify_peer_name"=>false,
			),
		);
		$result = $this->_jsonHelper->jsonDecode(file_get_contents($url, false, stream_context_create($arrContextOptions)));
		return $result['success'];
	}
}