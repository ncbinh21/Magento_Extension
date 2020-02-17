<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 26/10/2018
 * Time: 22:39
 */

namespace Forix\CatalogImport\Model\Import;
use \Magento\Framework\Filesystem\DriverPool;

class Uploader extends \Magento\CatalogImportExport\Model\Import\Uploader
{

    /**
     * HTTP scheme
     * used to compare against the filename and select the proper DriverPool adapter
     * @var string
     */
    private $httpScheme = 'http://';

    public function move($fileName, $renameFileOff = false)
    {
        if ($renameFileOff) {
            $this->setAllowRenameFiles(false);
        }
        if (preg_match('/\bhttps?:\/\//i', $fileName, $matches)) {
            $url = str_replace([$matches[0],' '], ['', '%20'], $fileName);
            if ($matches[0] === $this->httpScheme) {
                $read = $this->_readFactory->create($url, DriverPool::HTTP);
            } else {
                $read = $this->_readFactory->create($url, DriverPool::HTTPS);
            }
            //only use filename (for URI with query parameters)
            $parsedUrlPath = parse_url($url, PHP_URL_PATH);
            if ($parsedUrlPath) {
                $urlPathValues = explode('/', $parsedUrlPath);
                if (!empty($urlPathValues)) {
                    $fileName = end($urlPathValues);
                }
            }
            $fileName = preg_replace('/[^a-z0-9\._-]+/i', '', $fileName);
            $this->_directory->writeFile(
                $this->_directory->getRelativePath($this->getTmpDir() . '/' . $fileName),
                $read->readAll()
            );
        }
        $filePath = $this->_directory->getRelativePath($this->getTmpDir() . '/' . $fileName);
        $this->_setUploadFile($filePath);
        $destDir = $this->_directory->getAbsolutePath($this->getDestDir());
        $result = $this->save($destDir);
        unset($result['path']);
        $result['name'] = self::getCorrectFileName($result['name']);
        return $result;
    }

}