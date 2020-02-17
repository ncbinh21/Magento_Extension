<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Fpc
 */


namespace Amasty\Fpc\Block\Adminhtml\System\Config;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Module\Manager;

class XmlSitemap extends Field
{
    /**
     * @var Magento\Framework\Module\Manager
     */
    private $manager;

    public function __construct(
        Manager $manager,
        \Magento\Backend\Block\Template\Context $context,
        array $data = []
    ) {
        $this->manager = $manager;
        parent::__construct($context, $data);
    }

    public function render(AbstractElement $element)
    {
        if ($this->manager->isEnabled('Amasty_XmlSitemap')) {
            $element->setValue(__('Installed'));
            $element->setHtmlId('amasty_is_instaled');
            $url = $this->getUrl('adminhtml/system_config/edit', ['section' => 'amxmlsitemap']);
            $element->setComment(__('Specify XML Google&reg; Sitemap settings properly. See more details '
                . '<a href="%1" target="_blank">here</a>',
                $url
            ));
        } else {
            $element->setValue(__('Not Installed'));
            $element->setHtmlId('amasty_not_instaled');
            $element->setComment(__('Automatically generate XML Sitemaps in an efficient way.'
                . ' Let your FPC Crawler use XML sitemap for creating the queue for crawling.'
                . ' See more details <a target="_blank" href="https://amasty.com/magento-xml-google-sitemap.html?utm_source=extension&amp;utm_medium=backend&amp;utm_campaign=m2_fpc_to_xmlsitemap">here</a>')
            );
        }

        return parent::render($element);
    }
}
