<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */


namespace Amasty\Shopby\Controller\Adminhtml\Group;

use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\Controller\Result\JsonFactory;

class InlineEdit extends \Amasty\Shopby\Controller\Adminhtml\Group
{
    /**
     * @var JsonFactory
     */
    private $jsonFactory;

    /**
     * @var \Magento\Framework\Json\DecoderInterface
     */
    private $decoder;

    /**
     * InlineEdit constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Amasty\Shopby\Model\GroupAttrFactory $groupAttrFactory
     * @param \Magento\Backend\Model\SessionFactory $sessionFactory
     * @param \Magento\Framework\Json\DecoderInterface $decoder
     * @param JsonFactory $jsonFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Amasty\Shopby\Model\GroupAttrFactory $groupAttrFactory,
        \Magento\Backend\Model\SessionFactory $sessionFactory,
        TypeListInterface $typeList,
        \Magento\Framework\Json\DecoderInterface $decoder,
        JsonFactory $jsonFactory
    ) {
        $this->jsonFactory = $jsonFactory;
        $this->decoder = $decoder;
        parent::__construct($context, $coreRegistry, $resultPageFactory, $groupAttrFactory, $sessionFactory, $typeList);
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $resultJson = $this->jsonFactory->create();
        $error = false;
        $messages = [];
        $postItems = $this->getRequest()->getParam('items', []);

        if (!($this->getRequest()->getParam('isAjax') && count($postItems))) {
            return $resultJson->setData([
                'messages' => [__('Please correct the data sent.')],
                'error' => true,
            ]);
        }
        foreach ($postItems as $key => $item) {
            $id = $item['group_id'];
            $model = $this->groupAttrFactory->create();
            if ($id) {
                $model->load($id);
            }
            if (!$model->getId() && $id) {
                return $resultJson->setData([
                    'messages' => [__('This group no longer exists.')],
                    'error' => true,
                ]);
            }
            try {
                $options = $item;
                if (!is_array($item['option'])) {
                    $options['option'] = $this->decoder->decode($item['option']);
                }

                $model->setData($this->beforeSetData($options));
                $model->save();
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $messages[] = $e->getMessage();
                $error = true;
            } catch (\RuntimeException $e) {
                $messages[] = $e->getMessage();
                $error = true;
            } catch (\Exception $e) {
                $messages[] = __('Something went wrong while saving the group.');
                $error = true;
            }
        }

        return $resultJson->setData([
            'messages' => $messages,
            'error' => $error
        ]);
    }

    /**
     * @param $data
     * @return mixed
     */
    private function beforeSetData($data)
    {
        if (isset($data['option'])) {
            $data['attribute_options'] = [];
            $data['attribute_values'] = [];
            foreach ($data['option'] as $value) {
                if (isset($value['checked']) && $value['checked']) {
                    $data['attribute_' . $value['type_group'] . 's'][$value['id']] = [
                        'is_active' => ['value' => $value['value']],
                        'sort_order' => ['value' => $value['sort_order']]
                    ];
                }
            }
            unset($data['option']);
        }

        return $data;
    }
}
