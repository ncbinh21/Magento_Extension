<?php

namespace Forix\CatalogImport\Plugin;

class ImportPlugin
{
    protected $indexerRegistry;

    public function __construct(
        \Magento\Framework\Indexer\IndexerRegistry $indexerRegistry
    ) {
        $this->indexerRegistry = $indexerRegistry;
    }
    public function afterImportSource(\Magento\ImportExport\Model\Import $subject, $result)
    {
        $indexers = [
            'catalog_category_product',
            'catalog_product_price',
            'catalog_product_flat',
            'catalog_product_category',
            'catalog_product_attribute',
            'cataloginventory_stock',
            'catalogrule_rule',
            'targetrule_product_rule',
            'targetrule_rule_product'
        ];
        foreach ($indexers as $code) {
            $indexer = $this->indexerRegistry->get($code);
            $indexer->reindexAll();
        }
        return $result;
    }
}