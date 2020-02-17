<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Fpc
 */


namespace Amasty\Fpc\Block\Adminhtml\Form\Field\PageType;

use Magento\Backend\Block\Template;

/**
 * Class Element
 *
 * @package Amasty\Fpc\Block\Adminhtml\Form\Field\PageType
 *
 * @method array getValue()
 * @method Element setValue(array $value)
 * @method string getName()
 * @method Element setName(string $value)
 * */
class Element extends Template
{
    protected $_template = 'form/field/page_type.phtml';
    /**
     * @var \Amasty\Fpc\Model\Config\Source\PageType
     */
    private $pageTypeSource;

    public function __construct(
        Template\Context $context,
        \Amasty\Fpc\Model\Config\Source\PageType $pageTypeSource,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->pageTypeSource = $pageTypeSource;
    }

    public function getOptions()
    {
        return $this->pageTypeSource->toOptionArray();
    }

    public function getTypes()
    {
        $options = $this->getValue();

        uasort($options, function ($a, $b) {
            return $a['priority'] < $b['priority'] ? -1 : 1;
        });

        $labels = $this->pageTypeSource->toArray();

        foreach ($options as $key => &$option) {
            $option['label'] = $labels[$key];
        }

        return $options;
    }
}
