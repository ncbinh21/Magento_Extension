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
 * @package   mirasvit/module-report
 * @version   1.2.27
 * @copyright Copyright (C) 2017 Mirasvit (https://mirasvit.com/)
 */


namespace Mirasvit\Report\Block\Adminhtml\Email;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Grid\Extended as GridExtended;
use Magento\Backend\Helper\Data as BackendHelper;
use Mirasvit\Report\Api\Repository\EmailRepositoryInterface;

class Grid extends GridExtended
{
    /**
     * @var EmailRepositoryInterface
     */
    protected $emailRepository;

    /**
     * @param Context                  $context
     * @param BackendHelper            $backendHelper
     * @param EmailRepositoryInterface $emailRepository
     */
    public function __construct(
        Context $context,
        BackendHelper $backendHelper,
        EmailRepositoryInterface $emailRepository
    ) {
        $this->emailRepository = $emailRepository;

        parent::__construct($context, $backendHelper);
    }

    /**
     * {@inheritdoc}
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();

        $this->setId('emailGrid');
        $this->setDefaultSort('email_id');
        $this->setDefaultDir('asc');
        $this->setFilterVisibility(false);
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareCollection()
    {
        $this->setCollection($this->emailRepository->getCollection());

        return parent::_prepareCollection();
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareColumns()
    {
        $this->addColumn('email_id', [
                'header' => __('ID'),
                'type'   => 'number',
                'index'  => 'email_id',
            ]
        )->addColumn('title', [
                'header' => __('Title'),
                'index'  => 'title',
            ]
        )->addColumn('subject', [
                'header' => __('Subject'),
                'index'  => 'subject',
            ]
        )->addColumn('recipient', [
                'header' => __('Recipient'),
                'index'  => 'recipient',
            ]
        )->addColumn('action', [
                'header'   => __('Action'),
                'type'     => 'action',
                'getter'   => 'getId',
                'actions'  => [
                    [
                        'caption' => __('Send now'),
                        'url'     => [
                            'base' => 'report/email/send',
                        ],
                        'field'   => 'id',
                    ],
                ],
                'sortable' => false,
            ]
        );

        return parent::_prepareColumns();
    }

    /**
     * {@inheritdoc}
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('report/email/edit', ['id' => $row->getId()]);
    }
}
