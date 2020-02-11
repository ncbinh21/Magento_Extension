<?php
/**
 * Created by Forix.
 * User: Johnny
 * Date: 5/15/2017
 * Time: 3:01 PM
 */

namespace Forix\SearchNoResult\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class RemoveBlock implements ObserverInterface
{
    private $helper;
    protected $request;
    public function __construct(
        \Magento\Framework\App\Request\Http $request,
        \Forix\SearchNoResult\Helper\Data $searchNoResultHelper
    ) {
        $this->helper = $searchNoResultHelper;
        $this->request = $request;
    }

    public function execute(Observer $observer)
    {
        /** @var \Magento\Framework\View\Element\Template $block */
        $block = $observer->getBlock();
        if ($this->helper->isEnabled()
            && $block->getType() == 'Magento\Theme\Block\Html\Breadcrumbs'
            && $this->request->getFullActionName() == 'catalogsearch_result_index'
            && $this->helper->getConfigValue('remove_breadcrumbs')
        ) {
                $block->setTemplate(false);
        }
    }
}