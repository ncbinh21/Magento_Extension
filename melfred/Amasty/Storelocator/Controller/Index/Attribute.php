<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Storelocator
 */


namespace Amasty\Storelocator\Controller\Index;

class Attribute extends \Magento\Framework\App\Action\Action
{

    /**
     * @var \Amasty\Storelocator\Model\ResourceModel\Location\Collection
     */
    protected $locationCollection;
    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    protected $jsonEncoder;
    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Amasty\Storelocator\Model\ResourceModel\Location\Collection $locationCollection,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Framework\Registry $registry
    ) {
        parent::__construct($context);
        $this->locationCollection = $locationCollection;
        $this->jsonEncoder = $jsonEncoder;
        $this->registry = $registry;
    }

    public function execute()
    {
        $locationCollection = $this->locationCollection;

        $locationCollection->applyDefaultFilters();

        $params = $this->getRequest()->getParams();
        $result = [];
        if (isset($params['attributes'])) {
            parse_str($params['attributes'], $attributes);

            if (isset($attributes['attribute_id'])
                && !empty($attributes['attribute_id'])
                && isset($attributes['option'])
                && !empty($attributes['option'])
            ) {
                foreach ($attributes['attribute_id'] as $attributeId) {
                    if (isset($attributes['option'][$attributeId]) && $attributes['option'][$attributeId] != '') {
                        $result[(int)$attributeId] = (int)$attributes['option'][$attributeId];
                    }
                }
            }
        }

        if (count($result)) {
            $locationCollection->applyAttributeFilters($result);
        }

        $this->registry->register('amlocator_location', $locationCollection);

        $arrayCollection = [];

        if ($locationCollection->getSize()) {
            foreach ($locationCollection as $item) {
                $item->load($item->getId());
                $arrayCollection['items'][] = $item->getData();
            }
        } else {
            $this->messageManager->addNoticeMessage(__('Locations have not been found'));
        }

        $arrayCollection['totalRecords'] = isset($arrayCollection['items']) ? count($arrayCollection['items']) : 0;

        $this->_view->loadLayout();
        $left = $this->_view->getLayout()->getBlock('amlocatorAjax')->toHtml();

        $res = array_merge_recursive($arrayCollection, ['block' => $left]);

        $json = $this->jsonEncoder->encode($res);

        $this->getResponse()->setBody($json);

        return;
    }
}
