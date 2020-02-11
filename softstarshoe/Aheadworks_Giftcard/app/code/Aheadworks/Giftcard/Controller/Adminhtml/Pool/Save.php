<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Giftcard\Controller\Adminhtml\Pool;

use Aheadworks\Giftcard\Api\Data\Pool\CodeInterface as PoolCodeInterface;
use Aheadworks\Giftcard\Api\Data\PoolInterface;
use Aheadworks\Giftcard\Api\Data\PoolInterfaceFactory;
use Aheadworks\Giftcard\Api\Exception\ImportValidatorExceptionInterface;
use Aheadworks\Giftcard\Api\PoolRepositoryInterface;
use Magento\Backend\App\Action;
use Aheadworks\Giftcard\Api\PoolManagementInterface;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\File\Csv;
use Aheadworks\Giftcard\Model\Import\PoolCode as ImportPoolCode;

/**
 * Class Save
 *
 * @package Aheadworks\Giftcard\Controller\Adminhtml\Pool
 */
class Save extends Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Aheadworks_Giftcard::giftcard_pools';

    /**
     * @var PoolRepositoryInterface
     */
    private $poolRepository;

    /**
     * @var PoolManagementInterface
     */
    private $poolManagement;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * @var PoolInterfaceFactory
     */
    private $poolDataFactory;

    /**
     * @var Csv
     */
    private $csvProcessor;

    /**
     * ImportPoolCode
     */
    private $importPoolCode;

    /**
     * @param Context $context
     * @param PoolRepositoryInterface $poolRepository
     * @param PoolManagementInterface $poolManagement
     * @param DataObjectHelper $dataObjectHelper
     * @param DataPersistorInterface $dataPersistor
     * @param PoolInterfaceFactory $poolDataFactory
     * @param Csv $csvProcessor
     * @param ImportPoolCode $importPoolCode
     */
    public function __construct(
        Context $context,
        PoolRepositoryInterface $poolRepository,
        PoolManagementInterface $poolManagement,
        DataObjectHelper $dataObjectHelper,
        DataPersistorInterface $dataPersistor,
        PoolInterfaceFactory $poolDataFactory,
        Csv $csvProcessor,
        ImportPoolCode $importPoolCode
    ) {
        parent::__construct($context);
        $this->poolRepository = $poolRepository;
        $this->poolManagement = $poolManagement;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataPersistor = $dataPersistor;
        $this->poolDataFactory = $poolDataFactory;
        $this->csvProcessor = $csvProcessor;
        $this->importPoolCode = $importPoolCode;
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data = $this->getRequest()->getPostValue()) {
            $pool = $this->performSave($data);
            if (!empty($pool)) {
                $this->performGenerateCodes($pool, $data);
                $this->performImportCodes($pool, $data);

                if ($this->getRequest()->getParam('back') == 'edit') {
                    return $resultRedirect->setPath('*/*/edit', ['id' => $pool->getId()]);
                }
            } else {
                $this->dataPersistor->set('aw_giftcard_pool', $data);
                $id = isset($data['id']) ? $data['id'] : false;
                if ($id) {
                    return $resultRedirect->setPath('*/*/edit', ['id' => $id, '_current' => true]);
                }
                return $resultRedirect->setPath('*/*/new', ['_current' => true]);
            }
        }
        return $resultRedirect->setPath('*/*/');
    }

    /**
     * Perform save
     *
     * @param [] $data
     * @return PoolInterface
     * @throws LocalizedException|\Exception
     */
    private function performSave($data)
    {
        $pool = null;
        try {
            $id = isset($data['id']) ? $data['id'] : false;
            $dataObject = $id
                ? $this->poolRepository->get($id)
                : $this->poolDataFactory->create();
            $this->dataObjectHelper->populateWithArray(
                $dataObject,
                $data,
                PoolInterface::class
            );
            if (!$dataObject->getId()) {
                $dataObject->setId(null);
            }
            $pool = $this->poolRepository->save($dataObject);
            $this->dataPersistor->clear('aw_giftcard_pool');
            $this->messageManager->addSuccessMessage(__('Code pool was successfully saved'));
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage(
                $e,
                __('Something went wrong while saving the code pool')
            );
        }
        return $pool;
    }

    /**
     * Perform generate codes
     *
     * @param PoolInterface $pool
     * @param [] $data
     * @return PoolCodeInterface[]
     * @throws LocalizedException|\Exception
     */
    private function performGenerateCodes($pool, $data)
    {
        $generatedCodes = [];
        if (isset($data['generate_qty']) && (int)$data['generate_qty']) {
            try {
                $qty = (int)$data['generate_qty'];
                $generatedCodes = $this->poolManagement->generateCodesForPool($pool->getId(), $qty);
                $this->messageManager->addNoticeMessage(
                    __('Generated codes %1 of %2', count($generatedCodes), $qty)
                );
            } catch (ImportValidatorExceptionInterface $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage(
                    $e,
                    __('Something went wrong while generating codes into pool')
                );
            }
        }

        return $generatedCodes;
    }

    /**
     * Perform import codes
     *
     * @param PoolInterface $pool
     * @param [] $data
     * @return PoolCodeInterface[]
     * @throws ImportValidatorExceptionInterface|LocalizedException|\Exception
     */
    private function performImportCodes($pool, $data)
    {
        $importedCodes = [];
        if (isset($data['csv_file'][0])
            && isset($data['csv_file'][0]['full_path'])
            && $data['csv_file'][0]['full_path']
        ) {
            try {
                $codesRawData = $this->csvProcessor->getData($data['csv_file'][0]['full_path']);
                $importedCodes = $this->poolManagement->importCodesToPool($pool->getId(), $codesRawData);
                $message = count($importedCodes) == (count($codesRawData) - 1)
                    ? __('Imported codes %1 of %2', count($importedCodes), count($codesRawData) - 1)
                    : __(
                        'Imported codes %1 of %2. Details are available in log file: %3',
                        count($importedCodes),
                        count($codesRawData) - 1,
                        $this->importPoolCode->getUrlToLogFile()
                    );
                $this->messageManager->addNoticeMessage($message);
            } catch (ImportValidatorExceptionInterface $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage(
                    $e,
                    __('Something went wrong while importing codes to pool')
                );
            }
        }

        return $importedCodes;
    }
}
