<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Forix\QuoteLetter\Controller\Adminhtml\QuoteLetter;

use Magento\Catalog\Model\ProductFactory;
use Magento\Cms\Model\Wysiwyg as WysiwygModel;
use Magento\Framework\App\RequestInterface;
use Magento\Store\Model\StoreFactory;
use Psr\Log\LoggerInterface as Logger;
use Magento\Framework\Registry;

class Builder
{
    /**
     * @var \Forix\QuoteLetter\Model\QuoteLetterFactory
     */
    protected $quoteLetterFactory;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Cms\Model\Wysiwyg\Config
     */
    protected $wysiwygConfig;

    /**
     * @var StoreFactory
     */
    protected $storeFactory;

    /**
     * Constructor
     *
     * @param \Forix\QuoteLetter\Model\QuoteLetterFactory $quoteLetterFactory
     * @param Logger $logger
     * @param Registry $registry
     * @param WysiwygModel\Config $wysiwygConfig
     * @param StoreFactory|null $storeFactory
     */
    public function __construct(
        \Forix\QuoteLetter\Model\QuoteLetterFactory $quoteLetterFactory,
        Logger $logger,
        Registry $registry,
        WysiwygModel\Config $wysiwygConfig,
        StoreFactory $storeFactory = null
    ) {
        $this->quoteLetterFactory = $quoteLetterFactory;
        $this->logger = $logger;
        $this->registry = $registry;
        $this->wysiwygConfig = $wysiwygConfig;
        $this->storeFactory = $storeFactory ?: \Magento\Framework\App\ObjectManager::getInstance()
            ->get(\Magento\Store\Model\StoreFactory::class);
    }

    /**
     * Build product based on user request
     *
     * @param RequestInterface $request
     * @return \Forix\QuoteLetter\Model\QuoteLetter
     */
    public function build(RequestInterface $request)
    {
        $quoteLetterId = (int)$request->getParam('quoteletter_id');
        /** @var $quoteLetter \Forix\QuoteLetter\Model\QuoteLetter */
        $quoteLetter = $this->quoteLetterFactory->create();
        $quoteLetter->setStoreId($request->getParam('store', 0));
        $store = $this->storeFactory->create();
        $store->load($request->getParam('store', 0));


        $quoteLetter->setData('_edit_mode', true);
        if ($quoteLetterId) {
            try {
                $quoteLetter->load($quoteLetterId);
            } catch (\Exception $e) {
                $this->logger->critical($e);
            }
        }

        $setId = (int)$request->getParam('set');
        if ($setId) {
            $quoteLetter->setAttributeSetId($setId);
        }

        $this->registry->register('forix_quoteletter_quoteletter', $quoteLetter);
        $this->registry->register('current_store', $store);
        $this->wysiwygConfig->setStoreId($request->getParam('store'));
        return $quoteLetter;
    }
}
