<?php
/**
 * Created by: Bruce
 * Date: 2/2/16
 * Time: 16:55
 */

namespace Forix\CustomShipping\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

class Data extends AbstractHelper
{

    protected $_fileCsv;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Module\Dir\Reader $moduleReader,
        \Magento\Framework\File\Csv $fileCsv
    )
    {
        $this->_moduleReader = $moduleReader;
        $this->_fileCsv = $fileCsv;
        parent::__construct($context);
    }

    /**
     * @param $value
     * @return mixed
     */
    public function getConfigValue($value)
    {
        return $this->scopeConfig->getValue($value, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function getZipFull()
    {
        $directory = $this->_moduleReader->getModuleDir('etc', 'Forix_CustomShipping');
        $file = $directory . '/zip_codes_states.csv';

        if (file_exists($file)) {
            $aZips = array();
            $aZipCitys = array();
            $data = $this->_fileCsv->getData($file);
            // This skips the first line of your csv file, since it will probably be a heading. Set $i = 0 to not skip the first line.
            for ($i = 1; $i < count($data); $i++) {
                $aZipCitys[$data[$i][0]] = array($data[$i][3], $data[$i][4], $data[$i][5]); //4- Number of city column,5-Number of state column,6-Number of country column
                $aZips[] = $data[$i][0];
            }
        }

        return array($aZips, $aZipCitys);
    }

}