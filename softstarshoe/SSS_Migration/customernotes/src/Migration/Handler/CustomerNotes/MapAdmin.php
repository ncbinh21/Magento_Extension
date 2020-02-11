<?php
/**
 * Created by Nick Howard.
 * Job Title: Magento Developer
 * Project: softstarshoes.local
 */

namespace Migration\Handler\CustomerNotes;

use Migration\ResourceModel\Destination;
use Migration\ResourceModel\Source;
use Migration\ResourceModel\Record;
use Migration\Handler\AbstractHandler;
use Migration\Handler\HandlerInterface;

class MapAdmin extends AbstractHandler implements HandlerInterface
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
        $sourceUsers = $sourceAdapter->fetchPairs('SELECT `user_id`,`email` FROM `admin_user`');
        $destinationUsers = $destinationAdapter->fetchPairs('SELECT `email`,`user_id` FROM `admin_user`');
        foreach($sourceUsers as $_uid => $_email){
            if(array_key_exists($_email,$destinationUsers)){
                $this->map[$_uid] = $destinationUsers[$_email];
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
            $value = 1;
        }
        $recordToHandle->setValue($this->field, $value);
    }
}