<?php
namespace Migration\Handler\AheadworksHelpdesk;

use Migration\ResourceModel\Record;
use Migration\Handler\AbstractHandler;
use Migration\Handler\HandlerInterface;

/**
 * Handler for MessageContent
 */
class MessageContent extends AbstractHandler implements HandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(Record $recordToHandle, Record $oppositeRecord)
    {
        $this->validate($recordToHandle);
        $content = $recordToHandle->getValue($this->field);
        $content = strip_tags($content);
        $recordToHandle->setValue($this->field, $content);
    }
}
