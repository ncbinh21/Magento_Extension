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

class PageContent extends AbstractCreator
{
    public function __construct(
        ComponentRegistrarInterface $componentRegistrar, 
        \Magento\Framework\ObjectManagerInterface $objectManager, 
        array $data = [])
    {
        $data = array_merge($data, ['type' => 'pages', 'object_class' => 'Magento\Cms\Model\Page']);
        parent::__construct($componentRegistrar, $objectManager, $data);
    }

    public function import($fullFilePath, $fileName, $overwrite)
    {
        // TODO: Implement import() method.
        $fileContent = file_get_contents($fullFilePath);
        $pattern = '/<!--\n?\[\[PageTitle\]\]:(.*)\n?\[\[ContentHeading\]\]:(.*)\n?\[\[Layout\]\]:(.*)\n-->/';
        if('xml' != pathinfo($fullFilePath, PATHINFO_EXTENSION)) {
            preg_match($pattern, $fileContent, $matches);
            $pageTitle = $matches[1];
            $contentHeading = $matches[2];
            $layout = $matches[3];
            $pageContent = preg_replace($pattern, "", $fileContent);
            $pageContent = preg_replace("/<!--(.*?)-->/Uis", "", $pageContent);

            $key = str_replace(".phtml", "", $fileName);
            $layoutUpdateXml = '';
            if (file_exists($this->getWorkingDir() .'/'. $key . ".xml")) {
                $layoutUpdateXml = file_get_contents($this->getWorkingDir() .'/'. $key . ".xml");
                $layoutUpdateXml = preg_replace("/<!--(.*?)-->/Uis", "", $layoutUpdateXml);
            }
            try {
                $result = $this->create($pageTitle, $key, $contentHeading, $pageContent, $layout, $layoutUpdateXml, $overwrite);
                if ($result) {
                    $this->_addMessage(["Update|Insert [$pageTitle] Key [$key]"]);
                } else {
                    $this->_addMessage(["[$pageTitle] Key [$key] Already Exists Skip"]);
                }
            } catch (\Exception $ex) {
                $this->_addMessage(["Error where import page: $pageTitle Key $key"]);
            }
        }
    }

    /**
     * @param $title
     * @param $key
     * @param $contentHeading
     * @param $content
     * @param string $layout
     * @param $layoutUpdateXml
     * @param $overwrite
     * @return bool
     * @throws \Exception
     */
    public function create($title, $key, $contentHeading, $content, $layout = 'one_column', $layoutUpdateXml, $overwrite)
    {
        /**
         * @var $page \Magento\Cms\Model\Page
         */
        $page = $this->getObjectModel();
        $page = $page->load($key, "identifier");
        if ($page->getId()) {
            if (!$overwrite) {
                return false;
            }
        } else {
            $page->setStores([\Magento\Store\Model\Store::DEFAULT_STORE_ID]);
            $page->setIdentifier($key);
        }

        $page->setTitle($title);
        $page->setContentHeading($contentHeading);
        $page->setContent($content);
        $page->setRootTemplate($layout);
        $page->setPageLayout($layout);
        $page->setLayoutUpdateXml($layoutUpdateXml);
        $page->getResource()->save($page);
        return true;
    }

    public function getProcessCode()
    {
        // TODO: Implement getProcessCode() method.
        return 'createPage';
    }
}