<?php

namespace Forix\EmailToFriendPopup\Controller\Product;

class Sendmail extends \Magento\SendFriend\Controller\Product\Sendmail
{
    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $jsonFactory;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        \Magento\SendFriend\Model\SendFriend $sendFriend,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository,
        \Magento\Catalog\Model\Session $catalogSession,
        \Magento\Framework\Controller\Result\JsonFactory $jsonFactory
    ) {
        parent::__construct(
            $context,
            $coreRegistry,
            $formKeyValidator,
            $sendFriend,
            $productRepository,
            $categoryRepository,
            $catalogSession
        );
        $this->jsonFactory = $jsonFactory;

    }

    public function execute()
    {
        $helper = $this->_objectManager->get('Forix\EmailToFriendPopup\Helper\Data');
        if (!$helper->isAjaxAllowed()) {
            return parent::execute();
        }

        $json = $this->jsonFactory->create();

        if (!$this->_formKeyValidator->validate($this->getRequest())) {
            $resultResponse = array();
            $resultResponse['result'] = 0;
            $resultResponse['message'] = __('Invalid form key.');

            $json->setData($resultResponse);
            return $json;
        }

        if ($errors = $this->messageManager->getMessages(true)->getErrors()) {
            $resultResponse = array();
            $resultResponse['result'] = 0;
            foreach ($errors as $error) {
                $resultResponse['message'] = $error->getText();
            }
            $json->setData($resultResponse);
            return $json;
        }

        $product = $this->_initProduct();
        $data = $this->getRequest()->getPostValue();

        if (!$product || !$data) {
            $resultResponse = array();
            $resultResponse['result'] = 0;
            $resultResponse['message'] = __('Product not found!');
            $json->setData($resultResponse);
            return $json;
        }

        $categoryId = $this->getRequest()->getParam('cat_id', null);
        if ($categoryId) {
            try {
                $category = $this->categoryRepository->get($categoryId);
            } catch (NoSuchEntityException $noEntityException) {
                $category = null;
            }
            if ($category) {
                $product->setCategory($category);
                $this->_coreRegistry->register('current_category', $category);
            }
        }

        $this->sendFriend->setSender($this->getRequest()->getPost('sender'));
        $this->sendFriend->setRecipients($this->getRequest()->getPost('recipients'));
        $this->sendFriend->setProduct($product);

        $resultResponse = array();
        $resultResponse['result'] = 0;
        try {
            $validate = $this->sendFriend->validate();
            if ($validate === true) {
                $this->sendFriend->send();
                $resultResponse['result'] = 1;
                $resultResponse['message'] = __('The link to a friend was sent.');
            } else {
                if (is_array($validate)) {
                    $resultResponse['message'] = __('Invalid data.');
                } else {
                    $resultResponse['message'] = __('We found some problems with the data.');
                }
            }
        } catch (\Exception $e) {
            $resultResponse['message'] = __('Some emails were not sent.');
        }

        $json->setData($resultResponse);
        return $json;
    }
}
