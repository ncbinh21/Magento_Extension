<?php

namespace Forix\FanPhoto\Model;

use Magento\Framework\Mail\Template\TransportBuilder;

class EmailSend {

	public function __construct(
		\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
		TransportBuilder $transportBuilder
	) {
		$this->transportBuilder = $transportBuilder;
		$this->scopeConfig = $scopeConfig;
	}

	public function execute() {
		$emailTitle = $this->scopeConfig->getValue('fanphoto/general/email_title', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
		$emailContent = $this->scopeConfig->getValue('fanphoto/general/email_content', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
		$emailFrom = $this->scopeConfig->getValue('fanphoto/general/email_from', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
		$emailTo = $this->scopeConfig->getValue('fanphoto/general/email_to', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
		$emailSetting = [
			'email_title'       => $emailTitle,
			'email_content'       => $emailContent
		];

		$postObject = new \Magento\Framework\DataObject();
		$postObject->setData( $emailSetting );

		$transport = $this->transportBuilder
			->setTemplateIdentifier( 'fanphoto_newsubmit_template' )
			->setTemplateOptions( [
				'area'  => \Magento\Framework\App\Area::AREA_FRONTEND,
				'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID
			] )
			->setTemplateVars( [ 'data' => $postObject ] )
			->setFrom( [ 'name' => 'SoftStarShoes', 'email' => $emailFrom ] )
			->addTo( [ $emailTo ] )
			->getTransport();
		$transport->sendMessage();
	}
}