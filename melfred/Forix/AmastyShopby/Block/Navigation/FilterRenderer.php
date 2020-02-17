<?php

namespace Forix\AmastyShopby\Block\Navigation;

use Amasty\Shopby\Helper\FilterSetting;
use Amasty\Shopby\Helper\Data as ShopbyHelper;
use Amasty\Shopby\Helper\UrlBuilder;
use Magento\Catalog\Model\Layer\Filter\FilterInterface;
use Magento\Catalog\Model\Layer\Resolver;

class FilterRenderer extends \Amasty\Shopby\Block\Navigation\FilterRenderer
{
	/**
	 * @var \Amasty\ShopbyBase\Api\Data\FilterSettingInterface
	 */
	private $filterSetting;

	public function __construct(
		\Magento\Framework\View\Element\Template\Context $context,
		FilterSetting $settingHelper,
		UrlBuilder $urlBuilder,
		ShopbyHelper $helper,
		\Amasty\Shopby\Helper\Category $categoryHelper,
		Resolver $resolver
	)
	{

		parent::__construct($context, $settingHelper, $urlBuilder, $helper, $categoryHelper, $resolver, $data=[]);
		$this->settingHelper = $settingHelper;
		$this->urlBuilder = $urlBuilder;
		$this->helper = $helper;
		$this->categoryHelper = $categoryHelper;
		$this->layer = $resolver->get();
	}

	public function render(FilterInterface $filter)
	{
		$this->filter = $filter;
		$setting = $this->settingHelper->getSettingByLayerFilter($filter);

		if ($filter instanceof \Amasty\Shopby\Model\Layer\Filter\Category) {
			$categoryTreeHtml = $this->getLayout()
				->createBlock(\Amasty\Shopby\Block\Navigation\FilterRenderer\Category::class)
				->setFilter($filter)
				->render();
			$this->assign('categoryTreeHtml', $categoryTreeHtml);
			$template = $this->getCustomTemplateForCategoryFilter($setting);
		} else {
			$template = $this->getTemplateByFilterSetting($setting);
		}


		if ($setting->getData('mode_checkbox_cdp')) {
			$template = 'Amasty_Shopby::layer/filter/custom/default.phtml';
			$this->setTemplate($template);
		} else {
			$this->setTemplate($template);
		}

		$this->assign('filterSetting', $setting);

		if ($this->filter instanceof \Amasty\Shopby\Api\Data\FromToFilterInterface) {
			$fromToConfig = $this->filter->getFromToConfig();
			$this->assign('fromToConfig', $fromToConfig);
		}

		$html = \Magento\LayeredNavigation\Block\Navigation\FilterRenderer::render($filter)
			. $this->getTooltipHtml($setting)
			. $this->settingHelper->getShowMoreButtonBlock($setting)->toHtml();
		return $html;
	}




}