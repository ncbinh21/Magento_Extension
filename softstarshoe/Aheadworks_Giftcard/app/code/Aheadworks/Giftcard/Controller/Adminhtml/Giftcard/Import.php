<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Giftcard\Controller\Adminhtml\Giftcard;

use Aheadworks\Giftcard\Api\GiftcardManagementInterface;
use Aheadworks\Giftcard\Model\Import\Exception\ImportValidatorException;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\Result\Json;
use Magento\Backend\App\Action;
use Magento\Framework\File\Csv;
use Aheadworks\Giftcard\Model\Import\GiftcardCode as ImportGiftcardCode;

/**
 * Class Import
 *
 * @package Aheadworks\Giftcard\Controller\Adminhtml\Giftcard
 */
class Import extends Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Aheadworks_Giftcard::giftcard_codes';

    /**
     * @var GiftcardManagementInterface
     */
    private $giftcardManagement;

    /**
     * @var Csv
     */
    private $csvProcessor;

    /**
     * ImportGiftcardCode
     */
    private $importGiftcardCode;

    /**
     * @param Context $context
     * @param GiftcardManagementInterface $giftcardManagement
     * @param Csv $csvProcessor
     * @param ImportGiftcardCode $importGiftcardCode
     */
    public function __construct(
        Context $context,
        GiftcardManagementInterface $giftcardManagement,
        Csv $csvProcessor,
        ImportGiftcardCode $importGiftcardCode
    ) {
        parent::__construct($context);
        $this->giftcardManagement = $giftcardManagement;
        $this->csvProcessor = $csvProcessor;
        $this->importGiftcardCode = $importGiftcardCode;
    }

    /**
     * Image uploader action
     *
     * @return Json
     */
    public function execute()
    {
        /** @var Json $resultJson */
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $result = [];
        try {
            if ($data = $this->getRequest()->getPostValue()) {
                $result = $this->performSave($data);
            }
        } catch (ImportValidatorException $e) {
            $result = [
                'messages' => $e->getMessage(),
                'error' => true
            ];
        } catch (\Exception $e) {
            $result = [
                'messages' => $e->getMessage(),
                'error' => true
            ];
        }
        return $resultJson->setData($result);
    }

    /**
     * Perform save
     *
     * @param [] $data
     * @return string[]
     */
    private function performSave($data)
    {
        $result = [
            'messages' => __('File is not uploaded')
        ];
        if (isset($data['csv_file'][0])
            && isset($data['csv_file'][0]['full_path'])
            && $data['csv_file'][0]['full_path']
        ) {
            $codesRawData = $this->csvProcessor->getData($data['csv_file'][0]['full_path']);
            $importedCodes = $this->giftcardManagement->importCodes($codesRawData);

            $result['messages'] = count($importedCodes) == (count($codesRawData) - 1)
                ? __('Imported codes %1 of %2', count($importedCodes), count($codesRawData) - 1)
                : __(
                    'Imported codes %1 of %2. Details are available in log file: %3',
                    count($importedCodes),
                    count($codesRawData) - 1,
                    $this->importGiftcardCode->getUrlToLogFile()
                );
        }

        return $result;
    }
}
