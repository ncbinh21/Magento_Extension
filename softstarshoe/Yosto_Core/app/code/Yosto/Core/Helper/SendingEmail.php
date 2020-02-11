<?php
/**
 * Copyright © 2017 x-mage2(Yosto). All rights reserved.
 * See README.md for details.
 */
namespace Yosto\Core\Helper;


use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Translate\Inline\StateInterface;

/**
 * Class SendingEmail
 * @package Yosto\Yosto\Helper
 */
class SendingEmail
{
    /**
     * @var TransportBuilder
     */
    protected $_transportBuilder;

    /**
     * @var Data
     */
    protected $_configData;

    /**
     * @var StateInterface
     */
    protected $_inlineTranslation;

    /**
     * @param TransportBuilder $transportBuilder
     * @param Data $configData
     * @param StateInterface $inlineTranslation
     */
    public function __construct(
        TransportBuilder $transportBuilder,
        Data $configData,
        StateInterface $inlineTranslation
    ) {
        $this->_configData = $configData;
        $this->_transportBuilder = $transportBuilder;
        $this->_inlineTranslation = $inlineTranslation;
    }

    /**
     * @param $emailSenderPath
     * @param $emailTemplatePath
     * @param $email
     * @param $templateVars
     * @param null $storeId
     */
    public function sendEmail($emailSenderPath, $emailTemplatePath, $email, $templateVars, $storeId = null)
    {
        try {
            $emailSender = $this->_configData->getEmailSender($emailSenderPath);

            $transport = $this->_transportBuilder
                ->setTemplateIdentifier(
                    $this->_configData->getEmailTemplate($emailTemplatePath)
                )
                ->setTemplateVars($templateVars)
                ->setTemplateOptions(
                    [
                        'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                        'store' => $storeId
                    ]
                )
                ->setFrom(
                    [
                        'name' => $this->_configData->getSystemEmailName($emailSender),
                        'email' => $this->_configData->getSystemEmailBySenderName($emailSender)
                    ]
                )
                ->addTo($email)
                ->getTransport();
            $transport->sendMessage();
            $this->_inlineTranslation->resume();
        } catch(\Exception $e) {

        }
    }

}