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
 * @package   mirasvit/module-email
 * @version   1.1.13
 * @copyright Copyright (C) 2017 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Email\Ui\Trigger\Form\Control;


use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Mirasvit\Email\Api\Data\TriggerInterface;

class SendTestButton extends GenericButton implements ButtonProviderInterface
{
    /**
     * @var \Mirasvit\Email\Model\Config
     */
    private $config;

    /**
     * SendTestButton constructor.
     *
     * @param \Mirasvit\Email\Model\Config          $config
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry           $registry
     */
    public function __construct(
        \Mirasvit\Email\Model\Config $config,
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry
    ) {
        $this->config = $config;

        parent::__construct($context, $registry);
    }


    /**
     * {@inheritDoc}
     */
    public function getButtonData()
    {
        $data = [];
        $triggerId = $this->getTriggerId();

        if ($triggerId) {
            $data = [
                'label' => __('Send Test Email'),
                'sort_order' => 25,
                'on_click' => 'trigger.sendTestEmail(
                    this,
                    false,
                    \'' . $this->config->getTestEmail() . '\',
                    \'' . $this->getSaveUrl() . '\'
                )',
                'data_attribute' => [
                    'mage-init' => [
                        'trigger' => []
                    ],
                ],
            ];
        }

        return $data;
    }

    /**
     * @return string
     */
    public function getSaveUrl()
    {
        return $this->getUrl('*/*/send/', [TriggerInterface::ID => $this->getTriggerId()]);
    }
}