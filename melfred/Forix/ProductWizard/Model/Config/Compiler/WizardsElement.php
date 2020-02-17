<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 11/10/2018
 * Time: 12:14
 */

namespace Forix\ProductWizard\Model\Config\Compiler;

use Magento\Framework\DataObject;
use Magento\Framework\Filesystem;
use Magento\Framework\Module\Dir;
use Magento\Framework\Module\Dir\Reader;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\TemplateEngine\Xhtml\CompilerInterface;
use Magento\Framework\View\TemplateEngine\Xhtml\Compiler\Element\ElementInterface;

class WizardsElement implements ElementInterface
{
    protected $_resource;
    protected $_connection;
    protected $_wizardCollection;

    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource
    )
    {
        $this->_resource = $resource;
    }

    protected function getConnection()
    {
        if (!$this->_connection) {
            $this->_connection = $this->_resource->getConnection();
        }

        return $this->_connection;
    }

    protected function getWizardCollection(){
        if(is_null($this->_wizardCollection)) {
            $connection = $this->getConnection();
            /** @var $resource \Magento\Framework\App\ResourceConnection */
            /** @var $connection \Magento\Framework\DB\Adapter\Pdo\Mysql */
            $select = $connection->select()->from($connection->getTableName('forix_productwizard_wizard'), ['wizard_id','identifier', 'title', 'step_count']);
            $this->_wizardCollection = $connection->fetchAll($select);
        }
        return $this->_wizardCollection;
    }

    /**
     * Compiles the Element node
     *
     * @param CompilerInterface $compiler
     * @param \DOMElement $node
     * @param DataObject $processedObject
     * @param DataObject $context
     * @return void
     */
    public function compile(CompilerInterface $compiler,
                            \DOMElement $node,
                            DataObject $processedObject,
                            DataObject $context)
    {
        $ownerDocument = $node->ownerDocument;
        $parentNode = $node->parentNode;

        $document = new \DOMDocument();

        $xmlConfigContent = [];

        foreach ($this->getWizardCollection() as $wizard) {
            
            $xmlConfigContent[] = '<group id="' . (str_replace('-', '_', $wizard['identifier'])) . '" translate="label" sortOrder="10" showInDefault="1" showInWebsite="1" showInStore="1">
    <label>' . $wizard['title'] . '</label>';

            $stepCount = $wizard['step_count'];
            for ($i = 1; $i <= $stepCount; $i++) {
                $xmlConfigContent[] = <<<XML
    <field id="step_{$i}_title"  translate="label" type="editor" sortOrder="2"  showInDefault="1" showInWebsite="1" showInStore="1">
        <label>Step {$i} Title</label>
    </field>
XML;
            }

            $xmlConfigContent[] = '
    <field id="required_item_set" translate="label" sortOrder="10" showInDefault="1" showInWebsite="1" showInStore="0">
        <label>Required Item Sets</label>
        <frontend_model>\Forix\ProductWizard\Block\System\Config\Form\Field\ItemSet</frontend_model>
        <backend_model>Magento\Config\Model\Config\Backend\Serialized\ArraySerialized</backend_model>
    </field>
</group>';
        }
        
        $newFragment = $ownerDocument->createDocumentFragment();
        if (count($xmlConfigContent)) {
            $document->loadXML('<config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">' . implode('', $xmlConfigContent). '</config>');

            foreach ($this->getChildNodes($document->documentElement) as $child) {
                $compiler->compile($child, $processedObject, $context);
            }
            foreach ($document->documentElement->childNodes as $child) {
                $newFragment->appendXML($document->saveXML($child));
            }
        }
        $parentNode->replaceChild($newFragment, $node);
    }



    /**
     * Get child nodes
     *
     * @param \DOMElement $node
     * @return \DOMElement[]
     */
    protected function getChildNodes(\DOMElement $node)
    {
        $childNodes = [];
        foreach ($node->childNodes as $child) {
            $childNodes[] = $child;
        }

        return $childNodes;
    }

}