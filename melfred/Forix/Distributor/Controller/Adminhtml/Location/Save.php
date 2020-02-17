<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 20/08/2018
 * Time: 17:01
 */

namespace Forix\Distributor\Controller\Adminhtml\Location;

use \Magento\Framework\Message\MessageInterface;
use \Magento\Framework\App\Filesystem\DirectoryList;
use \Magento\ImportExport\Model\Import\Adapter as ImportAdapter;
use \Magento\ImportExport\Model\Import\ErrorProcessing\ProcessingErrorAggregatorInterface;
use \Amasty\Storelocator\Model\LocationFactory;

class Save extends \Amasty\Storelocator\Controller\Adminhtml\Location\Save
{

    public function execute()
    {
        if ($this->getRequest()->getPostValue()) {
            try {
	            $model = $this->_location->create();
                $data = $this->getRequest()->getPostValue();
                $inputFilter = new \Zend_Filter_Input(
                    [],
                    [],
                    $data
                );
                $data = $inputFilter->getUnescaped();
                $id = $this->getRequest()->getParam('id');
                if ($id) {
                    $model->load($id);
                    if ($id != $model->getId()) {
                        throw new \Magento\Framework\Exception\LocalizedException(__('The wrong item is specified.'));
                    }
                }
                if (isset($data['rule']['actions'])) {
                    $data['actions'] = $data['rule']['actions'];
                }
                if (isset($data['stores']) && !$data['stores']) {
                    $data['stores'] = ',1,';
                }
                if (isset($data['stores']) && is_array($data['stores'])) {
                    $data['stores'] = ',' . implode(',', $data['stores']) . ',';
                }

                if (isset($data['state_id'])) {
                    $data['state'] = $data['state_id'];
                }

                unset($data['rule']);

                $model->addData($data);
                $model->loadPost($data); // rules

                $data['actions_serialize'] = $this->serializer->serialize($model->getActions()->asArray());

                $this->_prepareForSave($model);

                $session = $this->_objectManager->get('Magento\Backend\Model\Session');
                $session->setPageData($model->getData());
                $model->save();
                $this->messageManager->addSuccess(__('You saved the item.'));
                $this->doImportZipCode($model);
                $session->setPageData(false);
                if ($this->getRequest()->getParam('back')) {
                    return $this->_redirect('*/*/edit', ['id' => $model->getId()]);
                }

                return  $this->_redirect('*/*/');
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
                $id = (int)$this->getRequest()->getParam('id');
                if (!empty($id)) {
                    return $this->_redirect('*/*/edit', ['id' => $id]);
                }
                return $this->_redirect('*/*/new');
            } catch (\Exception $e) {
                if ($e->getCode() != \Magento\MediaStorage\Model\File\Uploader::TMP_NAME_EMPTY) {
                    $errorMessage = 'Disallowed file type.';
                } else {
                    $errorMessage = 'Something went wrong while saving the item data. Please review the error log.';
                }
                $this->messageManager->addError(
                    __($errorMessage)
                );
                $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
                $this->_objectManager->get('Magento\Backend\Model\Session')->setPageData($data);
                return $this->_redirect('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);
            }
        }
        return $this->_redirect('*/*/');
    }

    /**
     * @param $model \Amasty\Storelocator\Model\Location
     */
    protected function doImportZipCode($model)
    {
        $files = $this->getRequest()->getFiles();
        try {
            try {
                $request = $this->getRequest();
                $userName = $this->_auth->getUser()->getUserName();
                $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/distributor_action.log');
                $logger = new \Zend\Log\Logger();
                $logger->addWriter($writer);
                $logger->info("----------------Begin Action Info-----------");
                $logger->info($userName);
                $logger->info('doImportZipCode');
                $logger->debug($request->getParams());
                $logger->info("----------------End Action Info-----------");
            }catch (\Exception $e){
                $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/distributor_action.log');
                $logger = new \Zend\Log\Logger();
                $logger->addWriter($writer);
                $logger->info($e);
            }

            if ($model->getId()) {
                $field = 'import_zipcode';
                $fileName = $this->getRequest()->getFiles($field)['name'];
                $hasFileImport = !empty($fileName);
                if ($hasFileImport) {
                    $path = $this->_filesystem->getDirectoryRead(
                        DirectoryList::MEDIA
                    )->getAbsolutePath(
                        'amasty/amlocator/zipcode'
                    );

                    /**
                     * @var $uploader \Magento\MediaStorage\Model\File\Uploader
                     */
                    $uploader = $this->_fileUploaderFactory->create(['fileId' => $field]);
                    $uploader->setAllowedExtensions(['csv']);
                    $uploader->setAllowRenameFiles(true);
                    $result = $uploader->save($path, $files[$field]['name']);
                    $sourceFile = $result['path'] . "/" . $result['file'];
                    /**
                     * @TODO Import Uploaded File.
                     * @var $importModel \Magento\ImportExport\Model\Import
                     */
                    $importModel = $this->_objectManager->get(\Magento\ImportExport\Model\Import::class);

                    $data = array(
                        'entity' => 'distributor_zipcodes',
                        'behavior' => 'append',
                        'location_id' => $model->getId(),
                        $importModel::FIELD_NAME_VALIDATION_STRATEGY => ProcessingErrorAggregatorInterface::VALIDATION_STRATEGY_SKIP_ERRORS,
                        $importModel::FIELD_NAME_ALLOWED_ERROR_COUNT => 10,
                        $importModel::FIELD_FIELD_SEPARATOR => ',',
                        $importModel::FIELD_FIELD_MULTIPLE_VALUE_SEPARATOR => \Magento\ImportExport\Model\Import::DEFAULT_GLOBAL_MULTI_VALUE_SEPARATOR,
                    );


                    $importModel->setData($data);


                    $source = ImportAdapter::findAdapterFor(
                        $sourceFile,
                        $this->_objectManager->create('Magento\Framework\Filesystem')->getDirectoryWrite(DirectoryList::ROOT),
                        ","
                    );
                    $validationResult = $importModel->validateSource($source);
                    if ($validationResult) {
                        if ($importModel->isImportAllowed()) {
                            $importModel->importSource();

                            $errorAggregator = $importModel->getErrorAggregator();
                            if (!$errorAggregator->hasToBeTerminated()) {
                                $this->messageManager->addSuccessMessage("Zip Code Import successfully done");
                            }
                        }
                    } else {
                        $allErrors = $importModel->getErrorAggregator()->getAllErrors();
                        foreach ($allErrors as $error) {
                            $this->messageManager->addError($error->getErrorMessage());
                        }
                    }
                }
                return true;
            }
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }
        return false;
    }
}