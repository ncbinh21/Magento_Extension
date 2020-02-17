<?php
/**
 * Created by Nick Howard.
 * Job Title: Magento Developer

 */

namespace Forix\Product\Block\Adminhtml\Form\Field;

class GroundCondition extends \Magento\Framework\View\Element\Html\Select
{

    protected $attOptions;

    public function __construct(
        \Magento\Framework\View\Element\Context $context,
        \Forix\Product\Model\Source\GroundCondition $attOptions,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->attOptions = $attOptions;
    }


    public function setInputName($value)
    {
        return $this->setName($value);
    }

    /**
     * Parse to html.
     *
     * @return mixed
     */
    public function _toHtml()
    {
        if (!$this->getOptions()) {
            $attributes = $this->attOptions->toOptionArray();
            foreach ($attributes as $attribute) {
                $this->addOption($attribute['value'], $attribute['label']);
            }
        }

        return parent::_toHtml();
    }
}
