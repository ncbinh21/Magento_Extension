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
 * @package   mirasvit/module-seo
 * @version   2.0.24
 * @copyright Copyright (C) 2017 Mirasvit (https://mirasvit.com/)
 */


namespace Mirasvit\SeoFilter\Service;

use Mirasvit\SeoFilter\Api\Service\ParserServiceInterface;
use Mirasvit\SeoFilter\Api\Data\RewriteInterface;
use Magento\Framework\App\Request\Http as RequestHttp;
use Magento\Store\Model\StoreManagerInterface;
use Magento\UrlRewrite\Model\ResourceModel\UrlRewriteCollectionFactory;
use Mirasvit\SeoFilter\Api\Repository\RewriteRepositoryInterface;
use Mirasvit\SeoFilter\Helper\Url as UrlHelper;

class ParserService implements ParserServiceInterface
{
    /**
     * @param RequestHttp $request
     * @param StoreManagerInterface $storeManager
     * @param UrlRewriteCollectionFactory $urlRewrite
     * @param RewriteRepositoryInterface $rewriteRepository
     * @param UrlHelper $urlHelper
     */
    public function __construct(
        RequestHttp $request,
        StoreManagerInterface $storeManager,
        UrlRewriteCollectionFactory $urlRewrite,
        RewriteRepositoryInterface $rewriteRepository,
        UrlHelper $urlHelper
    ) {
        $this->request = $request;
        $this->storeManager = $storeManager;
        $this->urlRewrite = $urlRewrite;
        $this->urlHelper = $urlHelper;
        $this->rewriteRepository = $rewriteRepository;
    }

    /**
     * @return bool|array
     */
    public function parseFilterInformationFromRequest() {
        $params = [];
        $storeId = $this->storeManager->getStore()->getId();
        $requestString = trim($this->request->getPathInfo(), '/');
        $requestPathRewrite = $this->urlRewrite->create()->addFieldToFilter('entity_type', 'category')
            ->addFieldToFilter('redirect_type', 0)
            ->addFieldToFilter('store_id', $storeId)
            ->addFieldToFilter('request_path',$requestString);

        if ($requestPathRewrite->getSize() > 0) {
            return false;
        }

        $shortRequestString = substr($requestString, 0, strrpos($requestString, '/'));

        if ($suffix =  $this->urlHelper->getCategoryUrlSuffix()) {
            $shortRequestString = $shortRequestString . $suffix;
        }

        $rewriteItem = $this->urlRewrite->create()->addFieldToFilter('entity_type', 'category')
            ->addFieldToFilter('redirect_type', 0)
            ->addFieldToFilter('store_id', $storeId)
            ->addFieldToFilter('request_path',$shortRequestString)
            ->getFirstItem();

        $categoryId = $rewriteItem->getEntityId();

        if (!$categoryId) {
            return false;
        }

        $filterString = $this->getFilterString($requestString, $suffix);

        $rewriteCollection = $this->rewriteRepository->getCollection()
            ->addFieldToFilter(RewriteInterface::REWRITE, $filterString)
            ->addFieldToFilter(RewriteInterface::STORE_ID, $storeId);
        $filterInfo = [$filterString];

        if ($rewriteCollection->getSize() == 0) {
            $filterInfo = $this->getPreparedFilterInfo($filterString);
            $rewriteCollection = $this->rewriteRepository->getCollection()
                ->addFieldToFilter(RewriteInterface::REWRITE, ['in' => $filterInfo])
                ->addFieldToFilter(RewriteInterface::STORE_ID, $storeId);
        }

        if ($rewriteCollection->getSize() == count($filterInfo)) {
            foreach ($rewriteCollection as $rewrite) {
                if (!isset($params[$rewrite->getAttributeCode()])) {
                    $params[$rewrite->getAttributeCode()] = [];
                }
                $params[$rewrite->getAttributeCode()] = ($rewrite->getAttributeCode() == RewriteInterface::PRICE)
                    ? $rewrite->getPriceOptionId() : $rewrite->getOptionId();
            }
        } else {
            return false;
        }

        $parsedResult = [
            RewriteInterface::CATEGORY_ID => $categoryId,
            RewriteInterface::PARAMS => $params
        ];

        return $parsedResult;
    }

    /**
     * @param string $requestString
     * @param string $suffix
     * @return string
     */
    protected function getFilterString($requestString, $suffix)
    {
        $filterString = substr($requestString, strrpos($requestString, '/') + 1);

        if (substr($filterString, -strlen($suffix) == $suffix)) {
            $filterString = substr($filterString, 0, -strlen($suffix));
        }

        return $filterString;
    }

    /**
     * @param string $filterString
     * @return array
     */
    protected function getPreparedFilterInfo($filterString)
    {
        preg_match('/(.*?)' . RewriteInterface::FILTER_SEPARATOR . RewriteInterface::PRICE  . '/ims',
            $filterString,
            $matches
        );
        if (isset($matches[1])) {
            $filterStringFirstPart = $matches[1];
            $filterInfo = explode(RewriteInterface::FILTER_SEPARATOR, $filterStringFirstPart);
            array_push($filterInfo,
                str_replace($filterStringFirstPart . RewriteInterface::FILTER_SEPARATOR, '', $filterString)
            );
        } else {
            $filterInfo = explode(RewriteInterface::FILTER_SEPARATOR, $filterString);
        }

        return $filterInfo;
    }
}