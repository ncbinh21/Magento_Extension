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

use Magento\Framework\View\LayoutInterface;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Mirasvit\EmailDesigner\Api\Data\TemplateInterface;
use Mirasvit\Core\Service\YamlService as YamlParser;

/**
 * @method int getThemeId()
 * @method $this setThemeId($themeId)
 *
 * @method array getTemplateAreas()
 * @method $this setTemplateAreas($areas)
 *
 * @method string getTemplateSubject()
 * @method $this setTemplateSubject($subject)
 *
 * @method string getTitle()
 * @method $this setTitle($title)
 */
class Template extends AbstractModel implements TemplateInterface
{
    /**
     * @var ThemeFactory
     */
    protected $themeFactory;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var LayoutInterface
     */
    protected $layout;

    /**
     * @var array
     */
    protected $areas;

    /**
     * @var Theme
     */
    protected $theme;

    /**
     * Template constructor.
     *
     * @param ThemeFactory    $themeFactory
     * @param Config          $config
     * @param LayoutInterface $layout
     * @param Context         $context
     * @param Registry        $registry
     */
    public function __construct(
        ThemeFactory $themeFactory,
        Config $config,
        LayoutInterface $layout,
        Context $context,
        Registry $registry
    ) {
        $this->themeFactory = $themeFactory;
        $this->config = $config;
        $this->layout = $layout;

        parent::__construct($context, $registry);
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init('Mirasvit\EmailDesigner\Model\ResourceModel\Template');
    }

    /**
     * List of editable areas
     *
     * @return array
     */
    public function getAreas()
    {
        if ($this->areas == null) {
            if ($this->getTheme()) {
                $this->areas = $this->getTheme()->getAreas();
            } else {
                $this->areas = ['content' => 'Content'];
            }
        }

        return $this->areas;
    }

    /**
     * Set theme
     *
     * @param Theme $theme
     * @return $this
     */
    public function setTheme(Theme $theme)
    {
        $this->theme = $theme;

        return $this;
    }

    /**
     * Theme model
     *
     * @return Theme
     */
    public function getTheme()
    {
        if ($this->theme == null && $this->getThemeId()) {
            $this->theme = $this->themeFactory->create()
                ->load($this->getThemeId());
        }

        return $this->theme;
    }

    /**
     * Get editable area text by code
     *
     * @param string $code
     * @return string|bool
     */
    public function getAreaText($code)
    {
        if (isset($this->getTemplateAreas()[$code])) {
            return $this->getTemplateAreas()[$code];
        }

        return false;
    }

    /**
     * Set editable area text by code
     *
     * @param string $code
     * @param string $content
     * @return $this
     */
    public function setAreaText($code, $content)
    {
        $templateAreas = $this->getTemplateAreas();
        $templateAreas[$code] = $content;
        $this->setData('template_areas', $templateAreas);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getProcessedTemplateText($variables = [])
    {
        $tpl = $this->getTheme()->getTemplateText();

        $result = $this->processText($tpl, $variables);

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function getProcessedTemplateSubject($variables = [])
    {
        return $this->processText($this->getTemplateSubject(), $variables);
    }

    /**
     * Process text
     *
     * @param string $tpl
     * @param array  $variables
     * @return string
     */
    protected function processText($tpl, $variables)
    {
        foreach ($this->getTemplateAreas() as $code => $text) {
            $variables['area_' . $code] = $text;
        }

        $block = $this->layout->createBlock('Mirasvit\EmailDesigner\Block\Template');

        $result = $block->render($tpl, $variables);

        return $result;
    }

    /**
     * Export template
     *
     * @return string
     */
    public function export()
    {
        $this->setData('theme', $this->getTheme()->getTitle());

        $path = $this->config->getTemplatePath() . '/' . $this->getTitle() . '.json';

        file_put_contents($path, $this->toJson());

        return $path;
    }

    /**
     * Import template
     *
     * @param string $filePath
     * @return Template
     */
    public function import($filePath)
    {
        $parser = new YamlParser();
        $data = $parser->parse(file_get_contents($filePath));

        /** @var Template $model */
        $model = $this->getCollection()
            ->addFieldToFilter('title', $data['title'])
            ->getFirstItem();

        $model->addData($data);

        /** @var Theme $theme */
        $theme = $this->themeFactory->create()->getCollection()
            ->addFieldToFilter('title', $data['theme'])
            ->getFirstItem();

        $model->setThemeId($theme->getId())
            ->save();

        return $model;
    }
}
