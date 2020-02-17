<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-seo
 * @version   2.0.64
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Seo\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

class Version extends AbstractHelper
{
    /**
     * @var  \Magento\Framework\App\ProductMetadataInterface
     */
    protected $productMetadata;

    /**
     * @param \Magento\Framework\App\ProductMetadataInterface $productMetadata
     */
    public function __construct(
        \Magento\Framework\App\ProductMetadataInterface $productMetadata
    ) {
        $this->productMetadata = $productMetadata;
    }

    /**
     * Get Product version
     *
     * @return string
     */
    public function getVersion()
    {
        return $this->productMetadata->getVersion();
    }

    /**
     * Get Product edition
     *
     * @return string
     */
    public function getEdition()
    {
        return $this->productMetadata->getEdition();
    }

    /**
     * Check if Enterprise
     *
     * @return bool
     */
    public function isEe()
    {
        if ($this->getEdition() == 'Enterprise') {
            return true;
        }

        return false;
    }
}
