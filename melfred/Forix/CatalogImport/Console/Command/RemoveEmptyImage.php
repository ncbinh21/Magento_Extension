<?php 


namespace Forix\CatalogImport\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Magento\Catalog\Model\Product\Attribute\Source\Status as ProductStatus;


class RemoveEmptyImage extends Command {

	const NAME_ARGUMENT = "name";
    const NAME_OPTION = "option";

    protected $objectManager;
    protected $_addressRepository;
    protected $_productFactory;
    protected $_resourceIterator;
    protected $_customerRepository;
    protected $directoryData;
    protected $_addressRegistry;
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Framework\Model\ResourceModel\Iterator $resourceIterator,
        \Magento\Directory\Helper\Data $directoryData,
        \Magento\Customer\Model\AddressRegistry $addressRegistry,
        \Magento\Customer\Api\AddressRepositoryInterface $addressRepository
    ){
        $this->objectManager = $objectManager;
        $this->_addressRepository = $addressRepository;
        $this->_productFactory = $productFactory;
        $this->_resourceIterator = $resourceIterator;
        $this->directoryData = $directoryData;
        $this->_addressRegistry = $addressRegistry;
        parent::__construct();
    }
	/**
	 * {@inheritdoc}
	 */
	protected function execute(
		InputInterface $input,
		OutputInterface $output
	)
	{
	    $this->setAreaCode();
		$output->writeln("--------------Start update Product ----------");
        /**
         * @var $customerAccounts \Magento\Customer\Model\ResourceModel\Customer\Collection
         */
        $resource = $this->_productFactory->create()->getResource();
        $productTable = $resource->getTable('catalog_product_entity');
        $this->_resourceIterator->walk(
            "select entity_id from {$productTable}",
            [[$this, 'mappingBundlePrice']],
            [],
            $resource->getConnection());
        $output->writeln("\n-------------- Update Product complete ----------");
	}


    /**
     * @param $args array
     */
	public function mappingBundlePrice($args){
        /**
         * @var $product \Magento\Catalog\Model\Product
         */
        $product = $this->_productFactory->create()->load($args['row']['entity_id']);

        $productRepository = $this->objectManager->get(\Magento\Catalog\Api\ProductRepositoryInterface::class);
        if ($product) {
            try {
                $existingMediaGalleryEntries = $product->getMediaGalleryEntries();
                foreach ($existingMediaGalleryEntries as $key => $entry) {
                    if (false !== strpos($entry->getFile(), 'httpsmelfred.mage.forixstage.com')) {
                        unset($existingMediaGalleryEntries[$key]);
                    }
                }
                $product->setMediaGalleryEntries($existingMediaGalleryEntries);
                $productRepository->save($product);
                echo "\r\nUpdated Product| {$product->getSku()} | ";
            }catch (\Exception $e){}
        }
    }

    private function setAreaCode()
    {
        $areaCode = 'adminhtml';
        /** @var \Magento\Framework\App\State $appState */
        $appState = $this->objectManager->get('Magento\Framework\App\State');
        /*if($appState->getAreaCode()){
            return $this;
        }*/
        $appState->setAreaCode($areaCode);
        /** @var \Magento\Framework\ObjectManager\ConfigLoaderInterface $configLoader */
        $configLoader = $this->objectManager->get('Magento\Framework\ObjectManager\ConfigLoaderInterface');
        $this->objectManager->configure($configLoader->load($areaCode));
    }
    
	/**
	 * {@inheritdoc}
	 */
	protected function configure()
	{
		$this->setName("forix:remove_empty_image");
		$this->setDescription("Remove Image Empty Product");
		$this->setDefinition([
		    new InputArgument(self::NAME_ARGUMENT,InputArgument::OPTIONAL,"Name"),
		    new InputOption(self::NAME_OPTION,"-a",InputOption::VALUE_NONE,"Option functionality")
		]);
		parent::configure();
	}

}
