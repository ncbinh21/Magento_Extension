<?php

namespace Orange35\ImageConstructor\Plugin;

class Wishlist
{
    protected $wishlistHelper;

    protected $imageHelperFactory;

    /**
     * @param \Magento\Wishlist\Helper\Data $wishlistHelper
     * @param \Magento\Catalog\Helper\ImageFactory $imageHelperFactory
     */
    public function __construct(
        \Magento\Wishlist\Helper\Data $wishlistHelper,
        \Magento\Catalog\Helper\ImageFactory $imageHelperFactory
    ) {
        $this->wishlistHelper = $wishlistHelper;
        $this->imageHelperFactory = $imageHelperFactory;
    }

    public function afterGetSectionData(\Magento\Wishlist\CustomerData\Wishlist $subject, $result)
    {

        $collection = $this->wishlistHelper->getWishlistItemCollection();
        $collection->clear()->setPageSize($subject::SIDEBAR_ITEMS_NUMBER)
            ->setInStockFilter(true)->setOrder('added_at');

        $items = [];
        foreach ($collection as $wishlistItem) {
            $items[] = $this->getItemData($wishlistItem);
        }
        $data['items'] = $items;
        return array_replace_recursive($result, $data);
    }

    protected function getItemData(\Magento\Wishlist\Model\Item $wishlistItem)
    {
        $product = $wishlistItem->getProduct();
        return [
            'image' => $this->getImageData($product),
            'itemId' => $wishlistItem->getId(),
        ];
    }

    protected function getImageData($product)
    {
        /** @var \Magento\Catalog\Helper\Image $helper */
        $helper = $this->imageHelperFactory->create()
            ->init($product, 'wishlist_sidebar_block');

        $template = $helper->getFrame()
            ? 'Magento_Catalog/product/image'
            : 'Orange35_ImageConstructor/product/image_with_borders';

        return [
            'template' => $template,
        ];
    }
}