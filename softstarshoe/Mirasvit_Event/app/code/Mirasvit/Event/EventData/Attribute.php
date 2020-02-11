<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-event
 * @version   1.1.10
 * @copyright Copyright (C) 2017 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Event\EventData;


use Magento\Framework\DataObject;
use Magento\Framework\Model\AbstractModel;
use Mirasvit\Event\Api\Data\AttributeInterface;
use Mirasvit\Event\Api\Data\EventDataInterface;

class Attribute extends DataObject implements AttributeInterface
{
    /**
     * @var EventDataInterface
     */
    private $eventData;

    public function __construct(
        EventDataInterface $eventData,
        array $data = []
    ) {
        parent::__construct($data);

        $this->eventData = $eventData;
    }

    /**
     * {@inheritDoc}
     */
    public function getCode()
    {
        return $this->getData(self::CODE);
    }

    /**
     * {@inheritDoc}
     */
    public function getLabel()
    {
        return __($this->getData(self::LABEL));
    }

    /**
     * {@inheritDoc}
     */
    public function getOptions()
    {
        return $this->getData(self::OPTIONS);
    }

    /**
     * {@inheritDoc}
     */
    public function getType()
    {
        return $this->getData(self::TYPE);
    }

    /**
     * At first, the value is checked in the $dataObject itself,
     * but if it's empty the value is used from the Entity Model.
     *
     * {@inheritDoc}
     */
    public function getValue(AbstractModel $dataObject)
    {
        $model = $dataObject->getData($this->eventData->getIdentifier());

        // use value from Event Params if it exists there, otherwise use value from Entity Model
        $value = $dataObject->getData($this->getCode()) !== null
            ? $dataObject->getData($this->getCode())
            // if model is not of type DataObject return false
            : ($model instanceof \Magento\Framework\DataObject
                ? $model->getData($this->getCode())
                : false
            );

        return $this->getType() == EventDataInterface::ATTRIBUTE_TYPE_BOOL ? (int) $value : $value;
    }

    /**
     * {@inheritDoc}
     */
    public function getConditionClass()
    {
        return $this->eventData->getConditionClass() . '|' . $this->getCode();
    }
}
