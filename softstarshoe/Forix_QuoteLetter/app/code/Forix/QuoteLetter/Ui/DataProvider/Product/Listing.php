<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2 - Soft Star Shoes
 * Date: 2/1/18
 * Time: 2:36 AM
 */
namespace Forix\QuoteLetter\Ui\DataProvider\Product;

use Forix\QuoteLetter\Api\Data\QuoteLetterInterface;
use Forix\QuoteLetter\Api\QuoteLetterRepositoryInterface;

use Magento\Catalog\Api\Data\ProductLinkInterface;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Catalog\Api\ProductLinkRepositoryInterface;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Api\StoreRepositoryInterface;

use Magento\Catalog\Ui\DataProvider\Product\ProductDataProvider;
class Listing extends ProductDataProvider
{

    /**
     * @var RequestInterface
     * @since 101.0.0
     */
    protected $request;

    /**
     * @var QuoteLetterRepositoryInterface
     * @since 101.0.0
     */
    protected $quoteLetterRepository;

    /**
     * @var StoreRepositoryInterface
     * @since 101.0.0
     */
    protected $storeRepository;

    /**
     * @var ProductLinkRepositoryInterface
     * @since 101.0.0
     */
    protected $productLinkRepository;

    /**
     * @var QuoteLetterInterface
     */
    private $quoteLetter;


    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        RequestInterface $request,
        QuoteLetterRepositoryInterface $quoteLetterRepository,
        StoreRepositoryInterface $storeRepository,
        $addFieldStrategies = [],
        $addFilterStrategies = [],
        array $meta = [],
        array $data = [])
    {
        $this->request = $request;
        $this->quoteLetterRepository = $quoteLetterRepository;
        $this->storeRepository = $storeRepository;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $collectionFactory, $addFieldStrategies, $addFilterStrategies, $meta, $data);
    }

    /**
     * {@inheritdoc}
     * @since 101.0.0
     */
    public function getCollection()
    {
        /** @var Collection $collection */
        $collection = parent::getCollection();

        if ($this->getStore()) {
            $collection->setStore($this->getStore());
        }
        return $collection;
    }


    /**
     * @return QuoteLetterInterface|null
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function getQuoteLetter()
    {
        if (null !== $this->quoteLetter) {
            return $this->quoteLetter;
        }

        if (!($id = $this->request->getParam('quoteletter_id'))) {
            return null;
        }

        return $this->quoteLetter = $this->quoteLetterRepository->getById($id);
    }

}