<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 20/07/2018
 * Time: 17:04
 */

namespace Forix\CatalogImport\Model\Import\Product\Validator;

use Magento\CatalogImportExport\Model\Import\Product\RowValidatorInterface;
use \Magento\CatalogImportExport\Model\Import\Product\Validator\AbstractImportValidator;
/**
 * Class Quantity
 */
class Quantity extends AbstractImportValidator implements RowValidatorInterface
{
    /**
     * {@inheritdoc}
     */
    public function isValid($value)
    {
        $this->_clearMessages();
        if (!empty($value['qty']) && !is_numeric($value['qty'])) {
            $this->_addMessages(
                [
                    sprintf(
                        $this->context->retrieveMessageTemplate(self::ERROR_INVALID_ATTRIBUTE_TYPE),
                        'qty',
                        'decimal'
                    ),
                ]
            );
            return false;
        }
        return true;
    }
}
