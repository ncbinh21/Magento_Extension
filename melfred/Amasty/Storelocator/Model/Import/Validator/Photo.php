<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Storelocator
 */


namespace Amasty\Storelocator\Model\Import\Validator;

use \Amasty\Storelocator\Model\Import\Location as Location;

class Photo extends AbstractImportValidator implements RowValidatorInterface
{

    const URL_REGEXP = '|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i';

    const PATH_REGEXP = '#^(?!.*[\\/]\.{2}[\\/])(?!\.{2}[\\/])[-\w.\\/]+$#';

    /**
     * @param string $string
     * @return bool
     */
    protected function checkValidUrl($string)
    {
        return preg_match(self::URL_REGEXP, $string);
    }

    /**
     * @param string $string
     * @return bool
     */
    protected function checkPath($string)
    {
        return preg_match(self::PATH_REGEXP, $string);
    }

    /**
     * @param string $path
     * @return bool
     */
    protected function checkFileExists($path)
    {
        return file_exists($path);
    }

    /**
     * {@inheritdoc}
     */
    public function init($context)
    {
        return parent::init($context);
    }

    /**
     * Validate value
     *
     * @param mixed $value
     * @return bool
     */
    public function isValid($value)
    {
        $this->_clearMessages();
        $importParams = $this->context->getParameters();
        $imageDir = $importParams[\Magento\ImportExport\Model\Import::FIELD_NAME_IMG_FILE_DIR];
        $valid = true;
        if (isset($value[Location::COL_PHOTO]) && !empty($value[Location::COL_PHOTO])) {
            $photoFullPath = $imageDir . $value[Location::COL_PHOTO];
            if ($this->checkPath($photoFullPath)) {
                if (!$this->checkFileExists($photoFullPath)) {
                    $valid = false;
                }
            }

            if (!$this->checkPath($photoFullPath) && !$this->checkValidUrl($photoFullPath)) {
                $valid = false;
            }
        }
        if (isset($value[Location::MARKER]) && !empty($value[Location::MARKER])) {
            $markerFullPath = $imageDir . $value[Location::MARKER];
            if ($this->checkPath($markerFullPath)) {
                if (!$this->checkFileExists($markerFullPath)) {
                    $valid = false;
                }
            }

            if (!$this->checkPath($markerFullPath) && !$this->checkValidUrl($markerFullPath)) {
                $valid = false;
            }
        }
        if (!$valid) {
            $this->_addMessages([self::ERROR_INVALID_PHOTO]);
        }

        return $valid;
    }


}