<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Develop
 * Project Name: Japana Project
 * Date: 30/09/2017
 * Time: 23:19
 */
namespace Forix\Scripts\Model;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Component\ComponentRegistrar;
use Magento\Framework\Component\ComponentRegistrarInterface;

abstract class AbstractCreator extends \Magento\Framework\DataObject
{

    /**
     * @var null|string
     */
    protected $_moduleDirectory;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;
    

    /**
     * AbstractCreator constructor.
     * @param ComponentRegistrarInterface $componentRegistrar
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param array $data
     */
    public function __construct(
        ComponentRegistrarInterface $componentRegistrar,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        array $data = [])
    {
        parent::__construct($data);
        $this->_objectManager = $objectManager;
        $this->_moduleDirectory = $componentRegistrar->getPath(ComponentRegistrar::MODULE, 'Forix_Scripts');
    }

    public function getWorkingDir()
    {
        return $this->_moduleDirectory . '/'. 'Data'. '/'. $this->getData('type');
    }

    /**
     * @return \Magento\Framework\Model\AbstractModel|null
     */
    public function getObjectModel(){
        if($this->getData('object_class')) {
            return $this->_objectManager->create($this->getData('object_class'));
        }
        return null;
    }

    public function process($overwrite)
    {
        $dataDir = $this->getWorkingDir();
        $dirs = scandir($dataDir);
        foreach ($dirs as $fileName) {
            $fullFilePath = $dataDir . "/" . $fileName;
            if (is_file($fullFilePath)) {
                $this->import($fullFilePath, $fileName, $overwrite);
            }
        }
        return true;
    }
    
    protected function _addMessage($messages = [])
    {
        $_messages = $this->getData('messages');
        if ($_messages) {
            $_messages = array_merge($_messages, $messages);
        } else {
            $_messages = $messages;
        }
        $this->setData('messages', $_messages);
    }
    
    public abstract function import($fullFilePath, $fileName, $overwrite);
    public abstract function getProcessCode();
}