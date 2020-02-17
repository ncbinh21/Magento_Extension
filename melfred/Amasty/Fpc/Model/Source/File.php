<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Fpc
 */


namespace Amasty\Fpc\Model\Source;

use Amasty\Fpc\Model\Config;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Psr\Log\LoggerInterface;

class File implements SourceInterface
{
    /**
     * @var Config
     */
    private $config;
    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        Config $config,
        Filesystem $filesystem,
        LoggerInterface $logger
    ) {
        $this->config = $config;
        $this->filesystem = $filesystem;
        $this->logger = $logger;
    }

    /**
     * Return pages to crawl from file
     *
     * @param int    $queueLimit
     * @param string $eMessage
     *
     * @return array
     */
    public function getPages($queueLimit, $eMessage)
    {
        $result = [];
        $counter = 0;

        $allFiles = $this->config->getAllValuesByPath('amasty_fpc/crawler/file_path');
        foreach ($allFiles as $item) {
            if ($counter == $queueLimit) {
                break;
            }

            $filePath = $item['value'];
            $directoryRead = $this->filesystem->getDirectoryRead(DirectoryList::ROOT);

            if (!$directoryRead->isExist($filePath)) {
                $this->logger->warning($eMessage . __('but file does not exist with specified path: %1', $filePath));
                continue;
            }

            $fileContent = $directoryRead->readFile($filePath);
            $urls = preg_split('/[,\s]+/', $fileContent, -1, PREG_SPLIT_NO_EMPTY);

            if (false === $urls) {
                $this->logger->warning($eMessage . __('but Amasty Crawler could not parse this file: %1', $filePath));
                continue;
            }

            if (empty($urls)) {
                $this->logger->warning($eMessage . __('but this file is empty: %1', $filePath));
                continue;
            }

            foreach ($urls as $counter => $url) {
                if ($counter == $queueLimit) {
                    break;
                }

                $result[] = [
                    'rate' => 100,
                    'url'  => $url,
                ];
            }
        }

        return $result;
    }
}
