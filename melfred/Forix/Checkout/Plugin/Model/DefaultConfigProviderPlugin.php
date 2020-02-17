<?php
/**
 * Created by Mercurial
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 */
namespace Forix\Checkout\Plugin\Model;

class DefaultConfigProviderPlugin
{
    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * DefaultConfigProviderPlugin constructor.
     * @param \Magento\Checkout\Model\Session $checkoutSession
     */
    public function __construct(
        \Magento\Checkout\Model\Session $checkoutSession

    ) {
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * @param \Magento\Checkout\Model\DefaultConfigProvider $defaultConfigProvider
     * @param $result
     * @return mixed
     */
    public function afterGetConfig(\Magento\Checkout\Model\DefaultConfigProvider $defaultConfigProvider, $result)
    {
        $quote = $this->checkoutSession->getQuote();
        if (isset($result['totalsData']['items'])) {
            foreach ($result['totalsData']['items'] as $key => $item) {
                $itemQuote = $quote->getItemById($item['item_id']);
                if ($itemQuote) {
                    $result['totalsData']['items'][$key]['sku'] = $itemQuote->getSku();
                }
            }
        }
        return $result;
    }
}
