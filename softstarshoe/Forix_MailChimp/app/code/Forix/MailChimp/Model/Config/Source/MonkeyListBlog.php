<?php
/**
 * mc-magento2 Magento Component
 *
 * @category Ebizmarts
 * @package mc-magento2
 * @author Ebizmarts Team <info@ebizmarts.com>
 * @copyright Ebizmarts (http://ebizmarts.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @date: 3/28/17 10:57 AM
 * @file: MonkeyStore.php
 */

namespace Forix\MailChimp\Model\Config\Source;

class MonkeyListBlog implements \Magento\Framework\Option\ArrayInterface
{
    private $options = null;

    /**
     * MonkeyList constructor.
     * @param \Ebizmarts\MailChimp\Helper\Data $helper
     * @param \Magento\Framework\App\RequestInterface $request
     */
    public function __construct(
        \Ebizmarts\MailChimp\Helper\Data $helper,
        \Magento\Framework\App\RequestInterface $request
    ) {
        $storeId = (int) $request->getParam("store", 0);
        if($request->getParam('website',0)) {
            $scope = 'websites';
            $storeId = $request->getParam('website',0);
        }
        elseif($request->getParam('store',0)) {
            $scope = 'stores';
            $storeId = $request->getParam('store',0);
        }
        else {
            $scope = 'default';
        }

        if ($helper->getApiKey($storeId)) {
            try {
                $this->options = $helper->getApi()->lists->getLists($helper->getConfigValue(\Forix\MailChimp\Helper\Data::XML_PATH_LIST_BLOG, $storeId,$scope));
            } catch (\Exception $e) {
                $helper->log($e->getMessage());
            }
        }
    }
    public function toOptionArray()
    {
        if (is_array($this->options)) {
            $rc = [];
            if (isset($this->options['id'])) {
                $rc[] = ['value' => $this->options['id'], 'label' => $this->options['name']];
            }
        } else {
            $rc[] = ['value' => 0, 'label' => __('---No Data---')];
        }
        return $rc;
    }
    public function toArray()
    {
        $rc = [];
        $rc[$this->options['id']] = $this->options['name'];
        return $rc;
    }
}
