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



namespace Mirasvit\Email\Ui\Trigger\Listing\Column;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Mirasvit\Email\Api\Data\TriggerInterface;

class Actions extends AbstractColumn
{
    /** Url path */
    const URL_PATH_EDIT   = 'email/trigger/edit';
    const URL_PATH_DELETE = 'email/trigger/delete';

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    public function __construct(
        UrlInterface $urlBuilder,
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;

        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function prepareItem(array $item)
    {
        return [
            'edit'   => [
                'href'  => $this->urlBuilder->getUrl(self::URL_PATH_EDIT, [
                    TriggerInterface::ID => $item[TriggerInterface::ID],
                ]),
                'label' => __('Edit'),
            ],
            'delete' => [
                'href'    => $this->urlBuilder->getUrl(self::URL_PATH_DELETE, [
                    TriggerInterface::ID => $item[TriggerInterface::ID],
                ]),
                'label'   => __('Delete'),
                'confirm' => [
                    'title' => __('Delete item?'),
                ],
            ],
        ];
    }
}
