<?php
namespace Migration\Handler\AheadworksHelpdesk;

use Migration\ResourceModel\Record;
use Migration\Handler\AbstractHandler;
use Migration\Handler\HandlerInterface;

/**
 * Handler for TicketCheck
 */
class TicketCheck extends AbstractHandler implements HandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(Record $recordToHandle, Record $oppositeRecord)
    {
        $this->validate($recordToHandle);

        $archived = $recordToHandle->getValue('archived');
        if ($archived) {
            return false;
        }
        return true;
    }
}
