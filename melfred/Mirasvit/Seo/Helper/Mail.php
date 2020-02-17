<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-seo
 * @version   2.0.64
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Seo\Helper;

class Mail extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Email\Model\TemplateFactory
     */
    protected $emailTemplateFactory;

    /**
     * @var \Mirasvit\Seo\Model\Config
     */
    protected $config;

    /**
     * @var \Magento\Framework\App\Helper\Context
     */
    protected $context;

    /**
     * @param \Magento\Email\Model\TemplateFactory  $emailTemplateFactory
     * @param \Mirasvit\Seo\Model\Config            $config
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(
        \Magento\Email\Model\TemplateFactory $emailTemplateFactory,
        \Mirasvit\Seo\Model\Config $config,
        \Magento\Framework\App\Helper\Context $context
    ) {
        $this->emailTemplateFactory = $emailTemplateFactory;
        $this->config = $config;
        $this->context = $context;
        parent::__construct($context);
    }

    /**
     * @var array
     */
    public $emails = [];

    /**
     * @return \Mirasvit\Seo\Model\Config
     */
    protected function getConfig()
    {
        return $this->config;
    }

    /**
     * @return string
     */
    protected function getSender()
    {
        return 'general';
    }

    /**
     * @param string $templateName
     * @param string $senderName
     * @param string $senderEmail
     * @param string $recipientEmail
     * @param string $recipientName
     * @param string $variables
     * @return bool
     */
    protected function send($templateName, $senderName, $senderEmail, $recipientEmail, $recipientName, $variables)
    {
        if (!$senderEmail) {
            return false;
        }
        $template = $this->emailTemplateFactory->create();
        $template->setDesignConfig(['area' => 'backend'])
                 ->sendTransactional(
                     $templateName,
                     [
                     'name' => $senderName,
                     'email' => $senderEmail,
                     ],
                     $recipientEmail,
                     $recipientName,
                     $variables
                 );
        $text = $template->getProcessedTemplate($variables, true);
        $this->emails[] = ['text' => $text, 'recipient_email' => $recipientEmail, 'recipient_name' => $recipientName];

        return true;
    }

    /************************/
}
