<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2 - EE - Melfredborzall
 * Date: 22/04/2019
 * Time: 15:37
 */

namespace Forix\Minilogin\CustomerData;

use Magento\Customer\CustomerData\SectionSourceInterface;

class CustomerForm implements SectionSourceInterface
{
    protected $_view;
    protected $_httpContext;
    protected $_currentCustomer;

    public function __construct(
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer,
        \Magento\Framework\App\ViewInterface $view
    )
    {
        $this->_view = $view;
        $this->_httpContext = $httpContext;
        $this->_currentCustomer = $currentCustomer;
    }

    public function isLoggedIn()
    {
        return $this->_httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_AUTH);
    }

    /**
     * @return string
     */
    public function getUserFormHtml()
    {
        if ($this->isLoggedIn()) {
            $customerId = $this->_currentCustomer->getCustomerId();
            $block = $this->_view->getLayout()->createBlock(\Forix\Minilogin\Block\Customer\Account\Form::class, 'customer-logged-in-' . ($customerId),
                [
                    'data' => ['template' => 'Forix_Minilogin::account/links.phtml']
                ]
            );
        } else {
            $block = $this->_view->getLayout()->createBlock(\Forix\Minilogin\Block\Customer\Account\Form::class, 'customer-not-logged-in',
                [
                    'data' => ['template' => 'Forix_Minilogin::account/form.phtml']
                ]
            );
        }
        return $block->toHtml();
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getSectionData()
    {
        return [
            'frm_html' => $this->getUserFormHtml(),
            'isLogged' => $this->isLoggedIn()
        ];
    }
}