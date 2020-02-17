<?php
/**
 * Created by: Bruce
 * Date: 2/2/16
 * Time: 16:44
 */
namespace Forix\ReCaptcha\Block;

class Explicit extends \Magento\Framework\View\Element\Template {

	protected $_languages = array(
		'ar_DZ|ar_SA|ar_KW|ar_MA|ar_EG|az_AZ|' => 'ar',
		'bg_BG' => 'bg',
		'ca_ES' => 'ca',
		'zh_CN' => 'zh-CN',
		'zh_HK|zh_TW' => 'zh-TW',
		'hr_HR' => 'hr',
		'cs_CZ' => 'cs',
		'da_DK' => 'da',
		'nl_NL' => 'nl',
		'en_GB|en_AU|en_NZ|en_IE|cy_GB' => 'en-GB',
		'en_US|en_CA' => 'en',
		'fil_PH' => 'fil',
		'fi_FI' => 'fi',
		'fr_FR' => 'fr',
		'fr_CA' => 'fr-CA',
		'de_DE' => 'de',
		'de_AT)' => 'de-AT',
		'de_CH' => 'de-CH',
		'el_GR' => 'el',
		'he_IL' => 'iw',
		'hi_IN' => 'hi',
		'hu_HU' => 'hu',
		'gu_IN|id_ID' => 'id',
		'it_IT|it_CH' => 'it',
		'ja_JP' => 'ja',
		'ko_KR' => 'ko',
		'lv_LV' => 'lv',
		'lt_LT' => 'lt',
		'nb_NO' => 'no',
		'fa_IR' => 'fa',
		'pl_PL' => 'pl',
		'pt_BR' => 'pt-BR',
		'pt_PT' => 'pt-PT',
		'ro_RO' => 'ro',
		'ru_RU' => 'ru',
		'sr_RS' => 'sr',
		'sk_SK' => 'sk',
		'sl_SI' => 'sl',
		'es_ES|gl_ES' => 'es',
		'es_AR|es_CL|es_CO|es_CR|es_MX|es_PA|es_PE|es_VE' => 'es-419',
		'sv_SE' => 'sv',
		'th_TH' => 'th',
		'tr_TR' => 'tr',
		'uk_UA' => 'uk',
		'vi_VN' => 'vi'
	);

	/* @var $_recaptchaHelper \Forix\ReCaptcha\Helper\Data */
	protected $_reCaptchaHelper;

	public function __construct(\Magento\Framework\View\Element\Template\Context $context,
								\Forix\ReCaptcha\Helper\Data $reCaptchaHelper,
								array $data) {
		$this->_reCaptchaHelper = $reCaptchaHelper;
		parent::__construct($context, $data);
	}

    /**
     * @return mixed|string
     */
    protected function getLanguage(){
        if (!$this->_reCaptchaHelper->isEnabled()) {
            return '';
        }

        $language = $this->_scopeConfig->getValue("general/locale/code");
        $lang = 'en';

        foreach ($this->_languages as $options => $_lang) {
            if (stristr($options, $language)) {
                $lang = $_lang;
                break;
            }
        }
        return $lang;
    }
	/**
	 * Get the reCAPTCHA javascript code.
	 *
	 * @return string
	 */
	public function getRecaptchaScript($formId) {
        $lang = $this->getLanguage();
		return sprintf('https://www.google.com/recaptcha/api.js?onload=onloadCallback%s&render=explicit&hl=%s', $formId, $lang);
	}

	public function getRecaptchaScriptSrc() {
        $lang = $this->getLanguage();
		return sprintf('https://www.google.com/recaptcha/api.js?onload=onloadCallback&render=explicit&hl=%s', $lang);
	}
	/**
	 * Get the recaptcha theme setting.
	 *
	 * @return string
	 */
	public function getTheme() {
		return $this->_reCaptchaHelper->getTheme();
	}

	/**
	 * Get the recaptcha site key.
	 *
	 * @return string
	 */
	public function getSiteKey() {
		return $this->_reCaptchaHelper->getSiteKey();
	}
	/**
	 * Get the recaptcha secret key.
	 *
	 * @return string
	 */
	public function getSecretKey() {
		return $this->_reCaptchaHelper->getSecretKey();
	}

	protected function _beforeToHtml() {
		return parent::_beforeToHtml(); // TODO: Change the autogenerated stub
	}

}