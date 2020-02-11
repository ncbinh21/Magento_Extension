<?php
namespace Migration\Step\AheadworksHelpdesk;

use Migration\App\Step\AbstractIntegrity;
use Migration\Logger\Logger;
use Migration\App\ProgressBar;
use Migration\Reader\Groups;
use Migration\Reader\GroupsFactory;
use Migration\Reader\MapFactory;
use Migration\Reader\MapInterface;
use Migration\ResourceModel;
use Migration\Config;
use Migration\Exception;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;

/**
 * Class Integrity
 */
class Integrity extends AbstractIntegrity
{
    /**
     * @var Groups
     */
    private $groups;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var string
     */
    private $attachmentsPath;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @param ProgressBar\LogLevelProcessor $progress
     * @param Logger $logger
     * @param ResourceModel\Source $source
     * @param ResourceModel\Destination $destination
     * @param MapFactory $mapFactory
     * @param GroupsFactory $groupsFactory
     * @param Config $config
     * @param Filesystem $filesystem
     * @param string $mapConfigOption
     */
    public function __construct(
        ProgressBar\LogLevelProcessor $progress,
        Logger $logger,
        ResourceModel\Source $source,
        ResourceModel\Destination $destination,
        MapFactory $mapFactory,
        GroupsFactory $groupsFactory,
        Config $config,
        Filesystem $filesystem,
        $mapConfigOption = 'aw_hdu3_map_file'
    ) {
        parent::__construct($progress, $logger, $config, $source, $destination, $mapFactory, $mapConfigOption);
        $this->config = $config;
        $this->filesystem = $filesystem;
        $this->groups = $groupsFactory->create('aw_hdu3_groups_file');
        $this->attachmentsPath = $this->config->getOption('aw_hdu3_attachments_path');
    }

    /**
     * {@inheritdoc}
     */
    public function perform()
    {
        $this->progress->start($this->getIterationsCount());
        $srcDocuments = array_keys($this->groups->getGroup('source_documents'));

        $dstDocuments = [];
        foreach ($srcDocuments as $sourceDocumentName) {
            $dstDocuments[] = $this->map->getDocumentMap($sourceDocumentName, MapInterface::TYPE_SOURCE);
        }

        $this->check($srcDocuments, MapInterface::TYPE_SOURCE, false);
        $this->check($dstDocuments, MapInterface::TYPE_DEST, false);

        if (!$this->isAttachmentsPathExist()) {
            $this->logger->warn('Attachments folder does not exist!');
//            throw new Exception('Attachments folder does not exist!');
        }

        $this->progress->finish();
        return $this->checkForErrors();
    }

    /**
     * {@inheritdoc}
     */
    protected function getIterationsCount()
    {
        return count($this->groups->getGroup('source_documents')) * 2;
    }

    /**
     * Check if attachments path is exist
     *
     * @return bool
     */
    private function isAttachmentsPathExist()
    {
        /** @var \Magento\Framework\Filesystem\Directory\Read $mediaDirectory */
        $mediaDirectory = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);
        $fullFileName = $mediaDirectory->getAbsolutePath($this->attachmentsPath);
        if (file_exists($fullFileName)) {
            return true;
        }
        return false;
    }
}
