<?php
namespace Forix\AdvancedAttribute\Model;

use Magento\MediaStorage\Model\File\UploaderFactory;
use Magento\Framework\File\Uploader;

class Upload
{
    /**
     * uploader factory
     *
     * @var \Magento\Core\Model\File\UploaderFactory
     */
    protected $uploaderFactory;

    /**
     * constructor
     *
     * @param UploaderFactory $uploaderFactory
     */
    public function __construct(UploaderFactory $uploaderFactory)
    {
        $this->uploaderFactory = $uploaderFactory;
    }

    /**
     * upload file
     *
     * @param $input
     * @param $destinationFolder
     * @param $data
     * @return string
     * @throws \Magento\Framework\Model\Exception
     */
    public function uploadFileAndGetName($input, $destinationFolder, $data)
    {
        try {
            if (!empty($data[$input]['delete'])) {
                if (!empty($data[$input]['value'])) {
                    $this->removeImage($destinationFolder . $data[$input]['value']);
                }
                return '';
            } else {
                /**
                 * @var $uploader \Magento\MediaStorage\Model\File\Uploader
                 */
                $uploader = $this->uploaderFactory->create(['fileId' => $input]);
                $uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png', 'svg']);
                $uploader->setAllowRenameFiles(true);
                $uploader->setFilesDispersion(true);
                $uploader->setAllowCreateFolders(true);
                $result = $uploader->save($destinationFolder);
                if (!empty($result['file']) && isset($data[$input]['value'])) {
                    $this->removeImage($destinationFolder . $data[$input]['value']);
                }
                return $result['file'];
            }
        } catch (\Exception $e) {
            if ($e->getCode() != Uploader::TMP_NAME_EMPTY) {
                throw new \Exception($e->getMessage());
            } else {
                if (isset($data[$input]['value'])) {
                    return $data[$input]['value'];
                }
            }
        }
        return '';
    }

    public function removeImage($_imgRemove)
    {
        if (!empty($_imgRemove) && file_exists($_imgRemove)) {
            @unlink($_imgRemove);
        }

        return;
    }
}
