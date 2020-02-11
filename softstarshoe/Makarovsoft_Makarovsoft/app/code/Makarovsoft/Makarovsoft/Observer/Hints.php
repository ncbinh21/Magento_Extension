<?php
namespace Makarovsoft\Makarovsoft\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\Config\MutableScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Makarovsoft\Makarovsoft\Helper\Data as Helper;


class Hints implements ObserverInterface
{

    /**
     * @var Helper $helper
     */
    protected $_helper;

    /**
     * @var MutableScopeConfigInterface
     */
    protected $_mutableConfig;

    /**
     * @param Helper $helper
     * @param MutableScopeConfigInterface $mutableConfig
     */
    public function __construct(
        Helper $helper,
        MutableScopeConfigInterface $mutableConfig
    ) {
        $this->_helper          = $helper;
        $this->_mutableConfig   = $mutableConfig;
    }

    public function execute(Observer $observer)
    {
        $this->_helper->log($observer->getEvent()->getName(), true);

        if ($this->_helper->shouldShowTemplatePathHints()) {

            $this->_mutableConfig->setValue(
                Helper::XML_PATH_DEBUG_TEMPLATE_FRONT,
                1,
                ScopeInterface::SCOPE_STORE
            );

            $this->_mutableConfig->setValue(
                Helper::XML_PATH_DEBUG_TEMPLATE_ADMIN,
                1,
                ScopeInterface::SCOPE_STORE
            );

            $this->_mutableConfig->setValue(
                Helper::XML_PATH_DEBUG_BLOCKS,
                1,
                ScopeInterface::SCOPE_STORE
            );

            /*if ($this->_helper->getShowProfiler()) {
                $_SERVER['MAGE_PROFILER'] = 'html';
            }*/
        }
        return $this;
    }
}