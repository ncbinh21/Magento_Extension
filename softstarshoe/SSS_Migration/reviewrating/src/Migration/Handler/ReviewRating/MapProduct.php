<?php
/**
 * Created by Nick Howard.
 * Job Title: Magento Developer
 * Project: softstarshoes.local
 */

namespace Migration\Handler\ReviewRating;

use Migration\ResourceModel\Destination;
use Migration\ResourceModel\Source;
use Migration\ResourceModel\Record;
use Migration\Handler\AbstractHandler;
use Migration\Handler\HandlerInterface;

class MapProduct extends AbstractHandler implements HandlerInterface
{
    /**
     * @var array
     */
    protected $map = [];
    /**
     * @param Destination $destination
     */
    public function __construct(
        Source $source,
        Destination $destination
    ) {
        $sourceAdapter = $source->getAdapter()->getSelect()->getAdapter();
        $destinationAdapter = $destination->getAdapter()->getSelect()->getAdapter();
        $sourceProducts = $sourceAdapter->fetchPairs('SELECT `entity_id`,`sku` FROM `catalog_product_entity`');
        $destinationProducts = $destinationAdapter->fetchPairs('SELECT `sku`,`entity_id` FROM `catalog_product_entity`');
        foreach($sourceProducts as $_pid => $_sku){
            if(array_key_exists($_sku,$destinationProducts)){
                $this->map[$_pid] = $destinationProducts[$_sku];
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function handle(Record $recordToHandle, Record $oppositeRecord)
    {
        $this->validate($recordToHandle);
        $value = $recordToHandle->getValue($this->field);
        if (isset($this->map[$value])) {
            $value = $this->map[$value];
        } else{
            $value = 0;
        }
        $recordToHandle->setValue($this->field, $value);
    }
}