<?php

namespace Forix\Media\Block;

class Media extends \Magento\Framework\View\Element\Template
{

    /**
     * @var \Forix\Media\Model\ResourceModel\Video\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * Media constructor.
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Forix\Media\Model\ResourceModel\Video\CollectionFactory $collectionFactory
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Forix\Media\Model\ResourceModel\Video\CollectionFactory $collectionFactory,
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        $this->productRepository = $productRepository;
        $this->collectionFactory  = $collectionFactory;
        parent::__construct($context, $data);
    }

    /**
     * @return \Forix\Media\Model\ResourceModel\Video\Collection
     */
    public function getListVideo()
    {
        return $this->collectionFactory->create();
    }

    /**
     *
     */
    public function getUrlProduct($idProduct)
    {
        try{
            if($idProduct) {
                $product = $this->productRepository->getById($idProduct);
                return $product->getProductUrl();
            }
        } catch (\Exception $exception) {
            return null;
        }
    }
}