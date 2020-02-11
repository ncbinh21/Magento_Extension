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

use Magento\Newsletter\Model\Subscriber;
use Mirasvit\Event\Api\Data\EventDataInterface;
use Mirasvit\Event\EventData\Condition\SubscriberCondition;

class SubscriberData extends Subscriber implements EventDataInterface
{
    use ContextTrait;

    const ID = 'subscriber_id';

    public function getIdentifier()
    {
        return 'subscriber';
    }

    public function getConditionClass()
    {
        return SubscriberCondition::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getLabel()
    {
        return __('Subscriber');
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributes()
    {
        return [];
    }
}