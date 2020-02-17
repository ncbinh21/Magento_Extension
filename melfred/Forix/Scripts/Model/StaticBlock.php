<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Develop
 * Project Name: Japana Project
 * Date: 30/09/2017
 * Time: 23:16
 */

namespace Forix\Scripts\Model;
use Magento\Framework\Component\ComponentRegistrarInterface;

class StaticBlock extends AbstractCreator
{
    public function __construct(
        ComponentRegistrarInterface $componentRegistrar, 
        \Magento\Framework\ObjectManagerInterface $objectManager, 
        array $data = [])
    {
        $data = array_merge($data, ['type' => 'blocks', 'object_class' => '\Magento\Cms\Model\Block']);
        parent::__construct($componentRegistrar, $objectManager, $data);
    }

    /**
     * @param $fullFilePath
     * @param $fileName
     * @param $overwrite
     */
    public function import($fullFilePath, $fileName, $overwrite)
    {
        // TODO: Implement import() method.
        $fileContent = file_get_contents($fullFilePath);
        $pattern = '/<!--\n?\[\[Header\]\]:(.*)\n?-->/';
        preg_match($pattern, $fileContent, $matches);
        $title = $matches[1];
        $fileContent = preg_replace($pattern, "", $fileContent);
        $fileContent = preg_replace("/<!--(.*?)-->/Uis", "", $fileContent);
        $key = str_replace(".phtml", "", $fileName);
        try {
            $result = $this->create($title, $key, $fileContent, $overwrite);
            if ($result) {
                $this->_addMessage(["Update|Insert [$title] Key [$key]"]);
            } else {
                $this->_addMessage(["[$title] Key [$key] Already Exists Skip"]);
            }
        } catch (\Exception $ex) {
            return $this->_addMessage(["Error where import block: $title Key $key"]);
        }
    }

    /**
     * @param $title
     * @param $identifier
     * @param $content
     * @param $overwrite
     * @return boolean
     * @throws Core_Exception
     */
    public function create($title, $identifier, $content, $overwrite)
    {
        /**
         * @var $block \Magento\Cms\Model\Block
         */
        $block = $this->getObjectModel();
        $block->load($identifier, 'identifier');
        if ($block->getId()) {
            if (!$overwrite) {
                return false;
            }
        } else {
            $block->setIdentifier($identifier);
            $block->setStores(\Magento\Store\Model\Store::DEFAULT_STORE_ID);
        }
        $block->setContent($content);
        $block->setTitle($title);
        
        $block->getResource()->save($block);
        return true;
    }

    /**
     * @return string
     */
    public function getProcessCode()
    {
        // TODO: Implement getProcessCode() method.
        return 'CreateStaticBlock';
    }
}