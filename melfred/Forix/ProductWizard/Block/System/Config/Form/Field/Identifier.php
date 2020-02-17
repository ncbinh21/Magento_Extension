<?php
/**
 * Created by PhpStorm.
 * User: ubuntu
 * Date: 17/04/2019
 * Time: 15:50
 */

namespace Forix\ProductWizard\Block\System\Config\Form\Field;


class Identifier extends \Magento\Framework\View\Element\AbstractBlock
{
    public function toHtml()
    {
        $columnName = $this->getColumnName();
        $inputName = $this->getInputName();
        $column = $this->getColumn();
        $inputId = $this->getInputId();
        return  '<input type="text" id="' . $inputId .
            '"' .
            ' name="' .
            $inputName .
            '" value="<%- ' .
            $columnName .
            ' %>" ' .
            ($column['size'] ? 'size="' .
                $column['size'] .
                '"' : '') .
            ' class="' .
            (isset(
                $column['class']
            ) ? $column['class'] : 'input-text') . '"' . (isset(
                $column['style']
            ) ? ' style="' . $column['style'] . '"' : '') . ' disabled/>';
    }
}