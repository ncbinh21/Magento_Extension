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



namespace Mirasvit\Seo\Observer\System\Config;

use Magento\Framework\Event\ObserverInterface;
use Mirasvit\Seo\Model\Config as Config;

class MaxLengthInfoObserver implements ObserverInterface
{
    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     */
    public function __construct(
        \Magento\Framework\Message\ManagerInterface $messageManager
    ) {
        $this->messageManager = $messageManager;
    }

    /**
     * Info about Max Length for Meta Title and Max Length for Meta Description.
     *
     * @param string $e
     * @return $this
     */
    public function maxLengthInfo($e)
    {
        $controllreAction = $e->getEvent()->getControllerAction();
        if (!$controllreAction) {
            return $this;
        }

        $seoSection = false;
        $data = [];
        $params = $controllreAction->getRequest()->getParams();
        if (isset($params['section']) && $params['section'] == 'seo'
            && ($data = $controllreAction->getRequest()->getPost('groups'))) {
            $seoSection = true;
        }

        $this->metaTitleMaxLengthInfo($seoSection, $data);
        $this->metaDescriptionMaxLengthInfo($seoSection, $data);

        return $this;
    }

    /**
     * Add Info about Max Length for Meta Title
     *
     * @param bool  $seoSection
     * @param array $data
     * @return $this
     */
    protected function metaTitleMaxLengthInfo($seoSection, $data)
    {
        if ($seoSection
            && isset($data['extended']['fields']['meta_title_max_length']['value'])
            && ($metaTitleMaxLength = trim($data['extended']['fields']['meta_title_max_length']['value']))) {
            if (ctype_digit($metaTitleMaxLength) && (int) $metaTitleMaxLength < Config::META_TITLE_INCORRECT_LENGTH) {
                $this->messageManager->addNotice(__('"Max Length for Meta Title" value: "'.$metaTitleMaxLength
                        .'" less then '.Config::META_TITLE_INCORRECT_LENGTH
                        .'. Will be used default value "'.Config::META_TITLE_MAX_LENGTH.'".'));
            } elseif (!ctype_digit($metaTitleMaxLength)) {
                $metaTitleInfo = 'Wrong "Max Length for Meta Title" value: "'.$metaTitleMaxLength.'".'
                                        .' Have to be integer value.';
                $metaTitleMaxLength = (int) $metaTitleMaxLength;
                if ($metaTitleMaxLength <  Config::META_TITLE_INCORRECT_LENGTH) {
                    $metaTitleInfo .= ' Will be used recommended value "'.Config::META_TITLE_MAX_LENGTH.'".';
                } else {
                    $metaTitleInfo .= ' Will be used value "'.$metaTitleMaxLength.'".';
                }
                $this->messageManager->addWarning(__($metaTitleInfo));
            }
        }

        return $this;
    }

    /**
     * Add Info about Max Length for Meta Description
     *
     * @param bool  $seoSection
     * @param array $data
     * @return $this
     */
    protected function metaDescriptionMaxLengthInfo($seoSection, $data)
    {
        if ($seoSection
            && isset($data['extended']['fields']['meta_description_max_length']['value'])
            && ($metaDescriptionMaxLength = trim($data
                ['extended']
                ['fields']
                ['meta_description_max_length']
                ['value']))) {
            if (ctype_digit($metaDescriptionMaxLength) && (int)
                $metaDescriptionMaxLength < Config::META_DESCRIPTION_INCORRECT_LENGTH) {
                $this->messageManager->addNotice(__('"Max Length for Meta Description" value: "'
                        .$metaDescriptionMaxLength
                        .'" less then '.Config::META_DESCRIPTION_INCORRECT_LENGTH
                        .'. Will be used default value "'.Config::META_DESCRIPTION_MAX_LENGTH.'".'));
            } elseif (!ctype_digit($metaDescriptionMaxLength)) {
                $metaDescriptionInfo = 'Wrong "Max Length for Meta Description" value: "'.$metaDescriptionMaxLength.'".'
                                            .' Have to be integer value.';
                $metaDescriptionMaxLength = (int) $metaDescriptionMaxLength;
                if ($metaDescriptionMaxLength <  Config::META_DESCRIPTION_INCORRECT_LENGTH) {
                    $metaDescriptionInfo .= ' Will be used recommended value "'
                        .Config::META_DESCRIPTION_MAX_LENGTH.'".';
                } else {
                    $metaDescriptionInfo .= ' Will be used value "'.$metaDescriptionMaxLength.'".';
                }
                $this->messageManager->addWarning(__($metaDescriptionInfo));
            }
        }

        return $this;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $this->maxLengthInfo($observer);
    }
}
