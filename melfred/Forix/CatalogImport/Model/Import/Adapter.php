<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 20/07/2018
 * Time: 14:16
 */

namespace Forix\CatalogImport\Model\Import;

use Magento\Framework\Filesystem\Directory\Write;
use \Magento\ImportExport\Model\Import\AbstractSource;

class Adapter extends \Magento\ImportExport\Model\Import\Adapter
{

    /**
     * Adapter factory. Checks for availability, loads and create instance of import adapter object.
     *
     * @param string $type Adapter type ('csv', 'xml' etc.)
     * @param Write $directory
     * @param string $source
     * @param mixed $options OPTIONAL Adapter constructor options
     *
     * @return AbstractSource
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public static function factory($type, $directory, $source, $options = null)
    {
        if (!is_string($type) || !$type) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('The adapter type must be a non-empty string.')
            );
        }
        $adapterClass = 'Forix\CatalogImport\Model\Import\Source\\' .ucfirst(strtolower($type));

        if (!class_exists($adapterClass)) {
            return parent::factory($type, $directory, $source, $options);
        }
        $adapter = \Magento\Framework\App\ObjectManager::getInstance()->create($adapterClass, [
            'file' => $source,
            'directory' => $directory,
            'delimiter' => $options
        ]);

        if (!$adapter instanceof AbstractSource) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('Adapter must be an instance of \Magento\ImportExport\Model\Import\AbstractSource')
            );
        }

        return $adapter;
    }

    /**
     * Create adapter instance for specified source file.
     *
     * @param string $source Source file path.
     * @param Write $directory
     * @param mixed $options OPTIONAL Adapter constructor options
     *
     * @return AbstractSource
     */
    public static function findAdapterFor($source, $directory, $options = null)
    {
        return self::factory(pathinfo($source, PATHINFO_EXTENSION), $directory, $source, $options);
    }
}