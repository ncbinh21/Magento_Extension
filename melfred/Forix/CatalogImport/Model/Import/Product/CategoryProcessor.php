<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 20/07/2018
 * Time: 17:04
 */

namespace Forix\CatalogImport\Model\Import\Product;

class CategoryProcessor extends \Magento\CatalogImportExport\Model\Import\Product\CategoryProcessor
{
    /**
     * {@inheritdoc}
     */
    protected function initCategories()
    {
        if (empty($this->categories)) {
            $collection = $this->categoryColFactory->create();
            $collection->addAttributeToSelect('name')
                ->addAttributeToSelect('url_key')
                ->addAttributeToSelect('url_path');
            /* @var $collection \Magento\Catalog\Model\ResourceModel\Category\Collection */
            foreach ($collection as $category) {
                $structure = explode(parent::DELIMITER_CATEGORY, $category->getPath());
                $pathSize = count($structure);

                $this->categoriesCache[$category->getId()] = $category;
                if ($pathSize > 1) {
                    $path = [];
                    for ($i = 1; $i < $pathSize; $i++) {
                        $path[] = $collection->getItemById((int)$structure[$i])->getName();
                    }
                    $index = $this->standardizeString(
                        implode(self::DELIMITER_CATEGORY, $path)
                    );
                    $this->categories[trim($index)] = $category->getId();
                }
            }
        }
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function upsertCategory($categoryPath)
    {
        $index = $this->standardizeString($categoryPath);
        if (!isset($this->categories[$index])) {
            $pathParts = explode(self::DELIMITER_CATEGORY, $categoryPath);
            $parentId = \Magento\Catalog\Model\Category::TREE_ROOT_ID;
            $path = '';

            foreach ($pathParts as $pathPart) {
                $path .= $this->standardizeString($pathPart);
                if (!isset($this->categories[$path])) {
                    $this->categories[$path] = $this->createCategory($pathPart, $parentId);
                }
                $parentId = $this->categories[$path];
                $path .= self::DELIMITER_CATEGORY;
            }
        }

        return $this->categories[$index];
    }

    private function standardizeString($string)
    {
        return mb_strtolower($string);
    }
}