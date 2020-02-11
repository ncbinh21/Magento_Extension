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
 * @package   mirasvit/module-email-designer
 * @version   1.0.16
 * @copyright Copyright (C) 2017 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\EmailDesigner\Model;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Mirasvit\EmailDesigner\Api\Data\ThemeInterface;
use Mirasvit\Core\Service\YamlService as YamlParser;

/**
 * @method string getTitle()
 * @method $this setTitle($title)
 *
 * @method string getDescription()
 * @method $this setDescription($description)
 *
 * @method int getTemplateType()
 * @method $this setTemplateType($type)
 *
 * @method string getTemplateText()
 * @method $this setTemplateText($text)
 */
class Theme extends AbstractModel implements ThemeInterface
{
    /**
     * @var TemplateFactory
     */
    protected $templateFactory;

    /**
     * @var Config
     */
    protected $config;

    /**
     * Constructor
     *
     * @param TemplateFactory $templateFactory
     * @param Config          $config
     * @param Context         $context
     * @param Registry        $registry
     */
    public function __construct(
        TemplateFactory $templateFactory,
        Config $config,
        Context $context,
        Registry $registry
    ) {
        $this->templateFactory = $templateFactory;
        $this->config = $config;

        parent::__construct($context, $registry);
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init('Mirasvit\EmailDesigner\Model\ResourceModel\Theme');
    }

    /**
     * List of defined areas in template texts
     *
     * @return array
     */
    public function getAreas()
    {
        $areas = [];
        $matches = [];

        preg_match_all("/area\(['\"]([0-9A-Za-z_\-]*)['\"]*/", $this->getTemplateText(), $matches);

        foreach ($matches[1] as $code) {
            $label = $code;
            $label = str_replace('_', ' ', $label);

            $areas[$code] = ucwords($label);
        }

        return $areas;
    }

    /**
     * Theme preview content
     *
     * @param array $variables
     * @return string
     */
    public function getProcessedTemplateText($variables = [])
    {
        $template = $this->templateFactory->create();
        $template->setTheme($this)
            ->setTemplateAreas([]);

        return $template->getProcessedTemplateText($variables);
    }

    /**
     * Export theme
     *
     * @return string
     */
    public function export()
    {
        $path = $this->config->getThemePath() . '/' . $this->getTitle() . '.json';

        file_put_contents($path, $this->toJson());

        return $path;
    }

    /**
     * Import theme
     *
     * @param string $filePath
     * @return Theme
     */
    public function import($filePath)
    {
        $parser = new YamlParser();

        $data = $parser->parse(file_get_contents($filePath));

        $model = $this->getCollection()
            ->addFieldToFilter('title', $data['title'])
            ->getFirstItem();

        $model->addData($data)
            ->save();

        return $model;
    }
}
