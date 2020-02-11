<?php
/**
 * Copyright © 2016 x-mage2(Yosto). All rights reserved.
 * See README.txt for details.
 *
 */

namespace Yosto\InstagramConnect\Model\Template\Source;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;
use Magento\Eav\Model\Entity\Attribute\Source\SourceInterface;
use Magento\Framework\Data\OptionSourceInterface;
use Yosto\InstagramConnect\Model\TemplateFactory;

/**
 * Class Template
 * @package Yosto\InstagramConnect\Model\Template\Source
 */
class Template
    extends AbstractSource
    implements SourceInterface, OptionSourceInterface
{

    /**
     * @var TemplateFactory
     */
    protected $_templateFactory;

    /**
     * Group constructor.
     * @param TemplateFactory $templateFactory
     */
    public function __construct(TemplateFactory $templateFactory)
    {
        $this->_templateFactory = $templateFactory;
    }

    /**
     * Get a text for option value
     *
     * @param string|integer $value
     * @return string|bool
     */
    public function getOptionText($value)
    {
        foreach ($this->getAllOptions() as $option) {
            if ($option['value'] == $value) {
                return $option['label'];
            }
        }
        return false;
    }

    /**
     * Get all options
     *
     * @return array
     */
    public function getAllOptions($withEmpty = true)
    {
        if (!$this->_options) {
            $templates = $this->getTemplates();
            foreach ($templates as $template) {
                $this->_options[] = [
                    'value' => $template->getTemplateId(),
                    'label' => $template->getTitle(),
                ];
            }
        }

        if ($withEmpty) {
            if (!$this->_options) {
                return [['value' => 0, 'label' => __('None')]];
            } else {
                return array_merge(
                    [
                        [
                            'value' => 0,
                            'label' => __('None')
                        ]
                    ], $this->_options
                );
            }
        }

        return $this->_options;
    }

    /**
     * Get all Group
     * @return AbstractCollection
     */
    public function getTemplates()
    {
        $templates = $this->_templateFactory->create()->getCollection()->addFieldToFilter('status', 1);
        return $templates;
    }

    /**
     * Retrieve flat column definition
     *
     * @return array
     */
    public function getFlatColumns()
    {
        $attributeCode = $this->getAttribute()->getAttributeCode();
        return [
            $attributeCode => [
                'unsigned' => true,
                'default' => null,
                'extra' => null,
                'type' => Table::TYPE_INTEGER,
                'nullable' => true,
                'comment' => 'Custom Attribute Options  '
                    . $attributeCode
                    . ' column',
            ],
        ];
    }
}