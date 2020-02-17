<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Fpc
 */


namespace Amasty\Fpc\Model\Source;

use Amasty\Fpc\Model\Config\Source\QuerySource;
use Magento\Framework\ObjectManagerInterface;

class Factory
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    public function __construct(
        ObjectManagerInterface $objectManager
    ) {
        $this->objectManager = $objectManager;
    }

    /**
     * @param int $type
     *
     * @return SourceInterface
     */
    public function create($type)
    {
        switch ($type) {
            case QuerySource::SOURCE_TEXT_FILE:
                $className = 'File';
                break;
            case QuerySource::SOURCE_SITE_MAP:
                $className = 'Sitemap';
                break;
            default:
                $className = 'All';
        }

        return $this->objectManager->create('\Amasty\Fpc\Model\Source\\' . $className);
    }
}
