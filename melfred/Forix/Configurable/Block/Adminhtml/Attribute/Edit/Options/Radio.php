<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 07/08/2018
 * Time: 17:47
 */
namespace Forix\Configurable\Block\Adminhtml\Attribute\Edit\Options;

use \Magento\Swatches\Block\Adminhtml\Attribute\Edit\Options\AbstractSwatch;

class Radio extends AbstractSwatch
{

    /**
     * @var string
     */
    protected $_template = 'Forix_Configurable::catalog/product/attribute/radio.phtml';

    /**
     * Return json config for text option JS initialization
     *
     * @return array
     * @since 100.1.0
     */
    public function getJsonConfig()
    {
        $values = [];
        foreach ($this->getOptionValues() as $value) {
            $values[] = $value->getData();
        }

        $data = [
            'attributesData' => $values,
            'isSortable' => (int)(!$this->getReadOnly() && !$this->canManageOptionDefaultOnly()),
            'isReadOnly' => (int)$this->getReadOnly()
        ];

        return json_encode($data);
    }
}