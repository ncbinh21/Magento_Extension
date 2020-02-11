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



namespace Mirasvit\EmailDesigner\Block;

use Magento\Email\Model\TemplateFactory as EmailTemplateFactory;
use Magento\Framework\App\Area;
use Magento\Framework\View\Element\Template as ViewTemplate;
use Magento\Framework\View\Element\Template\Context;
use Magento\Store\Model\App\Emulation as AppEmulation;
use Magento\Store\Model\StoreManagerInterface;
use Mirasvit\EmailDesigner\Model\Config;
use Mirasvit\EmailDesigner\Model\Variable\Pool as VariablePool;

class Template extends ViewTemplate
{
    /**
     * @var EmailTemplateFactory
     */
    protected $emailTemplateFactory;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var AppEmulation
     */
    protected $appEmulation;

    /**
     * @var VariablePool
     */
    protected $variablePool;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @param EmailTemplateFactory $templateFactory
     * @param Config               $config
     * @param AppEmulation         $appEmulation
     * @param Context              $context
     * @param VariablePool         $variablePool
     */
    public function __construct(
        EmailTemplateFactory $templateFactory,
        Config $config,
        AppEmulation $appEmulation,
        Context $context,
        VariablePool $variablePool
    ) {
        $this->emailTemplateFactory = $templateFactory;
        $this->config = $config;
        $this->appEmulation = $appEmulation;
        $this->variablePool = $variablePool;
        $this->storeManager = $context->getStoreManager();

        parent::__construct($context);
    }

    /**
     * Call
     *
     * @param string $method
     * @param array  $args
     * @return array|bool|string
     */
    public function __call($method, $args)
    {
        $result = $this->variablePool->resolve($method, $args);

        if ($result === false) {
            $result = parent::__call($method, $args);
        }

        return $result;
    }

    /**
     * Get data
     *
     * @param string $name
     * @return array|bool|string
     */
    public function __get($name)
    {
        return $this->variablePool->resolve($name);
    }

    /**
     * Get area content
     *
     * @param string $area
     * @param bool   $default
     * @return string
     */
    public function area($area, $default = false)
    {
        if ($this->hasData('area_' . $area)) {
            $tplContent = $this->getData('area_' . $area);
            $block = $this->getLayout()->createBlock('Mirasvit\EmailDesigner\Block\Template');

            return $block->render($tplContent, $this->getData());
        }

        if ($this->variablePool->getContext()->getData('preview')) {
            if ($default) {
                return $default;
            }

            return true;
        }

        return "Area '$area' not defined";
    }

    /**
     * Render template
     *
     * @param string $tplContent
     * @param array  $variables
     * @return string
     */
    public function render($tplContent, $variables = [])
    {
        $this->variablePool->getContext()
            ->unsetData()
            ->setVariablePool($this->variablePool)
            ->addData($variables);

        $this->addData($variables);

        $tplPath = $this->config->getTmpPath() . '/' . time() . rand(1, 10000) . '.phtml';

        file_put_contents($tplPath, $tplContent);

        $html = $this->getHtml($tplPath);
        $template = $this->emailTemplateFactory->create();

        if (isset($variables['store_id'])) {
            $template->emulateDesign($variables['store_id']);
        } else {
            $template->emulateDesign($this->storeManager->getStore()->getId());
        }

        $template->setTemplateText($html)
            ->setIsPlain(false);

        $html = $template->getProcessedTemplate([]);


        unlink($tplPath);

        return $html;
    }

    /**
     * Get html
     *
     * @param string $tplPath
     * @return string
     */
    public function getHtml($tplPath)
    {
        ob_start();
        include $tplPath;
        $html = ob_get_clean();

        return $html;
    }
}
