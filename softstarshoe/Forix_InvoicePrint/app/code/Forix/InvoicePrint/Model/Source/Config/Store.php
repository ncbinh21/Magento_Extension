<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.2-CE - Soft Star Shoes
 * Date: 27
 * Time: 15:29
 */

namespace Forix\InvoicePrint\Model\Source\Config;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;
use Magento\Eav\Model\Entity\Attribute\Source\SourceInterface;
use Magento\Framework\Data\OptionSourceInterface;
use Magento\Store\Model\System\Store as SystemStore;

/**
 * Class Stores
 *
 * @package Forix\InvoicePrint\Model\Source\Config
 */
class Store extends AbstractSource implements SourceInterface, OptionSourceInterface
{

    /**
     * @var SystemStore
     */
    protected $systemStore;

    /**
     * @var array
     */
    protected $_options;

    /**
     * Store constructor.
     *
     * @param SystemStore $systemStore
     */
    public function __construct(
        SystemStore $systemStore
    ) {
        $this->systemStore = $systemStore;
    }

    /**
     * Retrieve option array with empty value
     *
     * @return string[]
     */
    public function getAllOptions()
    {
        if ($this->_options === null) {
            $this->_options = $this->systemStore->getStoreValuesForForm(false, true);
        }
        return $this->_options;
    }
}
