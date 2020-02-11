<?php
namespace Migration\Handler\AheadworksHelpdesk;

use Migration\ResourceModel\Source;
use Migration\ResourceModel\Record;
use Migration\Handler\AbstractHandler;
use Migration\Handler\HandlerInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Migration\Config;

/**
 * Handler for MessageAttachment
 */
class MessageAttachment extends AbstractHandler implements HandlerInterface
{
    /**
     * @var Source
     */
    private $source;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var string
     */
    private $attachmentsPath;

    /**
     * @param Source $source
     * @param Filesystem $filesystem
     * @param Config $config
     */
    public function __construct(
        Source $source,
        Filesystem $filesystem,
        Config $config
    ) {
        $this->source = $source;
        $this->filesystem = $filesystem;
        $this->config = $config;
        $this->attachmentsPath = $this->config->getOption('aw_hdu3_attachments_path');
    }

    /**
     * {@inheritdoc}
     */
    public function handle(Record $recordToHandle, Record $oppositeRecord)
    {
        $this->validate($recordToHandle);
        $ticketHistoryId = $recordToHandle->getValue($this->field);
        $attachmentFilename = $recordToHandle->getValue('content');

        $messageId = $this->getMessageId($ticketHistoryId);
        if ($messageId) {
            $recordToHandle->setValue($this->field, $messageId);
        } else {
            $recordToHandle->setValue($this->field, 0);
        }

        $attachment = $this->getAttachment($ticketHistoryId, $attachmentFilename);
        if ($attachment) {
            $recordToHandle->setValue('content', $attachment);
        }
    }

    /**
     * Get message id
     *
     * @param int $ticketHistoryId
     * @return int|bool
     */
    private function getMessageId($ticketHistoryId)
    {
        $adapter = $this->source->getAdapter();
        $query = $adapter->getSelect()
            ->from($this->source->addDocumentPrefix('aw_hdu3_ticket_message'), ['id'])
            ->where("history_id = ?", $ticketHistoryId)
        ;
        $result = $query->getAdapter()->fetchRow($query);
        if ($result) {
            return (int)$result['id'];
        }
        return false;
    }

    /**
     * Get attachment
     *
     * @param int $ticketHistoryId
     * @param string $attachmentFilename
     * @return string|bool
     */
    private function getAttachment($ticketHistoryId, $attachmentFilename)
    {
        /** @var \Magento\Framework\Filesystem\Directory\Read $mediaDirectory */
        $mediaDirectory = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);
        $fullFileName = $mediaDirectory->getAbsolutePath($this->attachmentsPath)
            . '/ticket_history' . '/' . $ticketHistoryId . '/' . $attachmentFilename;
        if (file_exists($fullFileName)) {
            $content = @file_get_contents($fullFileName);
            return $content;
        }
        return false;
    }
}
