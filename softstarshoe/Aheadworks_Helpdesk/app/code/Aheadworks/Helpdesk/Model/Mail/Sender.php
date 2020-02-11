<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Helpdesk\Model\Mail;

use Aheadworks\Helpdesk\Model\Config;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Exception\MailException;
use Magento\Framework\Message\ManagerInterface as MessageManagerInterface;

/**
 * Class Sender
 * @package Aheadworks\Helpdesk\Model\Mail
 */
class Sender
{
    /**
     * @var TransportBuilder
     */
    private $transportBuilder;

    /**
     * @var MessageManagerInterface
     */
    private $messageManager;

    /**
     * @param TransportBuilder $transportBuilder
     * @param MessageManagerInterface $messageManager
     */
    public function __construct(
        TransportBuilder $transportBuilder,
        MessageManagerInterface $messageManager
    ) {
        $this->transportBuilder = $transportBuilder;
        $this->messageManager = $messageManager;
    }

    /**
     * Send email
     * @param $emailData
     * @param bool $needReplyTo
     */
    public function sendEmail($emailData, $needReplyTo = true)
    {
        /* Added by Forix: hide sender's lastname and email address */
        if($needReplyTo && !empty($emailData['gateway']) && is_string($emailData['gateway'])){
            $emailData['from']['email'] = $emailData['gateway'];
            $emailData['from']['name'] = $this->splitName($emailData['from']['name'])['first_name'];
        }
        /* ---------------------------------------------------------*/

        $this->transportBuilder
            ->setTemplateIdentifier($emailData['template_id'])
            ->setTemplateOptions([
                'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                'store' => $emailData['store_id']
            ])
            ->setTemplateVars($emailData)
            ->setFrom($emailData['from'])
            ->addTo($emailData['to'], $emailData['sender_name'])
        ;
        if (isset($emailData['cc_recipients'])) {
            $this->transportBuilder->addCc($emailData['cc_recipients']);
        }
        if ($needReplyTo) {
            $this->transportBuilder->setReplyTo($emailData['gateway']);
        }
        $transport = $this->transportBuilder->getTransport();
        if(!empty($emailData['attachments'])){
            /**
             * @var $message \Magento\Framework\Mail\Message
             */
            $message = $transport->getMessage();
            $file_info = new \finfo();
            foreach($emailData['attachments'] as $attachment){
                if(!empty($attachment['length'])){
                    $message->createAttachment($attachment['content'],
                        $file_info->buffer($attachment['content'],FILEINFO_MIME_TYPE),
                        \Zend_Mime::DISPOSITION_ATTACHMENT,
                        \Zend_Mime::ENCODING_BASE64,
                        $attachment['name']
                    );
                }
            }
        }
        try {
            $transport->sendMessage();
        } catch (MailException $e) {
            $this->messageManager->addErrorMessage($e->getMessage(), Config::EMAIL_ERROR_MESSAGE_GROUP);
        }
    }
    public function splitName($name) {
        $parts = array();

        while ( strlen( trim($name)) > 0 ) {
            $name = trim($name);
            $string = preg_replace('#.*\s([\w-]*)$#', '$1', $name);
            $parts[] = $string;
            $name = trim( preg_replace('#'.$string.'#', '', $name ) );
        }

        if (empty($parts)) {
            return false;
        }

        $parts = array_reverse($parts);
        $name = array();
        $name['first_name'] = $parts[0];
        $name['middle_name'] = (isset($parts[2])) ? $parts[1] : '';
        $name['last_name'] = (isset($parts[2])) ? $parts[2] : ( isset($parts[1]) ? $parts[1] : '');

        return $name;
    }
}
