<?php

/**
 * Project: LoveMyBubbles.
 * User: Hidro Le
 * Title: Magento Develop
 * Date: 8/4/17
 * Time: 2:08 PM
 */

namespace Forix\ImportHelper\Model\Import\RawData\ColumnValidator\Product;

use \Forix\ImportHelper\Model\Images\InterfaceImagesIO;
use Forix\ImportHelper\Model\Import\RawData\ColumnValidator\AbstractColumnType;
use Zend_Validate_Exception;

class Gallery extends AbstractColumnType
{
    /**
     * @var InterfaceImagesIO
     */
    protected $driverClass;

    protected $_context;

    /**
     * Media constructor.
     * @param string $linkedWith
     * @param InterfaceImagesIO $driverClass
     * @throws \Exception
     */
    public function __construct(InterfaceImagesIO $driverClass, $linkedWith = '')
    {
        parent::__construct($linkedWith);
        $this->initDriver($driverClass);
    }

    /**
     * @param $driverClass
     * @return InterfaceImagesIO
     * @throws \Exception
     */
    protected function initDriver($driverClass)
    {
        if (!($driverClass instanceof InterfaceImagesIO)) {
            throw new \Exception("DriverClass mush be instanceof InterfaceImagesIO");
        }
        $this->driverClass = $driverClass->init();
        return $this->driverClass;
    }


    protected function checkRemoteFile($url)
    {
        $url = str_replace('', '%20', $url);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        // don't download content
        curl_setopt($ch, CURLOPT_NOBODY, 1);
        curl_setopt($ch, CURLOPT_FAILONERROR, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $result = curl_exec($ch);
        curl_close($ch);
        if ($result === FALSE) {
            return false;
        }
        return true;
    }

    /**
     * @param $value
     * @param $rowData
     * @return bool
     */
    public function validate($value, $rowData)
    {
        $this->_clearMessages();
        if ($value) {
            $values = preg_split("/(".PHP_EOL."|,)/", $value);
            /*foreach ($values as $_value) {
                $url = 'https://melfred.mage.forixstage.com/imageimport/import/pngs/' . $_value;
                if (!$this->checkRemoteFile($url)) {
                    $this->_addMessages([self::ERROR_MEDIA_URL_NOT_ACCESSIBLE . ":" . "{$value}"]);
                    return false;
                }
            }*/
        }
        return true;
    }
}