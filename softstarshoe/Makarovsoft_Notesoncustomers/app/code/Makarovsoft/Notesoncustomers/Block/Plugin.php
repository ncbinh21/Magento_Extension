<?php

namespace Makarovsoft\Notesoncustomers\Block;

class Plugin
{

    /**
     * @var \Makarovsoft\Notesoncustomers\Block\Adminhtml\Order\Notesoncustomers
     */
    protected $notes;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Makarovsoft\Notesoncustomers\Block\Adminhtml\Order\Notesoncustomers $notes,
        array $data = []
    )
    {
        $this->notes = $notes;
    }


    /**
     * @param \Magento\Sales\Block\Adminhtml\Order\Create\Sidebar\Wishlist $subject
     * @param \Closure $proceed
     * @return mixed
     */
    public function aroundToHtml($subject,
                                 \Closure $proceed) {
        $result = $proceed();



        if (!$subject->getLayout()->hasElement('notesoncustomers')) {
            $result .= '<script>setTimeout("order.sidebarApplyChanges()", 1000);</script>';
        }

        return $result;
    }

}

