<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 20/07/2018
 * Time: 17:04
 */
namespace Forix\CatalogImport\Model\Import\Product\Validator;


class Media extends \Magento\CatalogImportExport\Model\Import\Product\Validator\Media
{
    protected function checkPath($string)
    {
        return true; //preg_match('#^(?!.*[\\/]\.{2}[\\/])(?!\.{2}[\\/])[-\w.\\/\\%]+$#', $string) || parent::checkPath($string);
    }
}