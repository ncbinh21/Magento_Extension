<?php

namespace Forix\Custom\Rewrite\Indexer\Handler;

use Magento\Framework\App\ResourceConnection\SourceProviderInterface;

class AttributeHandler extends \Magento\Framework\Indexer\Handler\AttributeHandler
{
    /**
     * Prepare SQL for field and add it to collection
     *
     * @param SourceProviderInterface $source
     * @param string $alias
     * @param array $fieldInfo
     * @return void
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function prepareSql(SourceProviderInterface $source, $alias, $fieldInfo)
    {
        if (isset($fieldInfo['bind'])) {
            if (!method_exists($source, 'joinAttribute')) {
                return;
            }

            $source->joinAttribute(
                $fieldInfo['name'],
                $fieldInfo['entity'] . '/' . $fieldInfo['origin'],
                $fieldInfo['bind'],
                null,
                'left'
            );
        } else {
            $source->addFieldToSelect($fieldInfo['origin'], 'left');
        }
    }
}