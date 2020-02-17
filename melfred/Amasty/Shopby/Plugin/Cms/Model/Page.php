<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */


namespace Amasty\Shopby\Plugin\Cms\Model;

use Magento\Framework\Exception\NoSuchEntityException;

class Page
{
    /**
     * @var \Amasty\Shopby\Model\Cms\PageFactory
     */
    protected $pageFactory;

    /**
     * @var \Amasty\Shopby\Api\CmsPageRepositoryInterface
     */
    protected $pageRepository;

    /**
     * Page constructor.
     * @param \Amasty\Shopby\Model\Cms\PageFactory $pageFactory
     * @param \Amasty\Shopby\Api\CmsPageRepositoryInterface $cmsPageRepository
     */
    public function __construct(
        \Amasty\Shopby\Model\Cms\PageFactory $pageFactory,
        \Amasty\Shopby\Api\CmsPageRepositoryInterface $cmsPageRepository
    ) {
        $this->pageFactory = $pageFactory;
        $this->pageRepository = $cmsPageRepository;
    }

    /**
     * @param \Magento\Cms\Model\Page $page
     * @param \Closure $proceed
     * @param string $key
     * @param null $index
     * @return mixed
     */
    public function aroundGetData(
        \Magento\Cms\Model\Page $page,
        \Closure $proceed,
        $key = '',
        $index = null
    ) {
        $data = $proceed($key, $index);

        if (($key === '' || $key === \Amasty\Shopby\Model\Cms\Page::VAR_SETTINGS) &&
            $page->getId() &&
            (!is_array($data) || !array_key_exists(\Amasty\Shopby\Model\Cms\Page::VAR_SETTINGS, $data))
        ) {
            try {
                $shopbyPage = $this->pageRepository->getByPageId($page->getId());
                if ($shopbyPage->getId()) {
                    $data[\Amasty\Shopby\Model\Cms\Page::VAR_SETTINGS] = $shopbyPage->getData();
                }
            } catch (NoSuchEntityException $e) {
                //skip
            }
        }

        return $data;
    }

    /**
     * @param \Magento\Cms\Model\Page $page
     * @param \Magento\Cms\Model\Page $returnPage
     * @return \Magento\Cms\Model\Page
     */
    public function afterSave(
        \Magento\Cms\Model\Page $page,
        \Magento\Cms\Model\Page $returnPage
    ) {
        $settings = $returnPage->getData('amshopby_settings');
        if (is_array($settings)) {
            try {
                $shopbyPage = $this->pageRepository->getByPageId($page->getId());
            } catch (NoSuchEntityException $e) {
                $shopbyPage = $this->pageFactory->create();
            }
            $shopbyPage->setData(array_merge(['page_id' => $page->getId()], $settings));
            $this->pageRepository->save($shopbyPage);
        }

        return $returnPage;
    }
}
