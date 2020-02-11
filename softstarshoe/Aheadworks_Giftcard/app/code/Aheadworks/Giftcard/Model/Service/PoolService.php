<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Giftcard\Model\Service;

use Aheadworks\Giftcard\Api\PoolManagementInterface;
use Aheadworks\Giftcard\Api\PoolCodeRepositoryInterface;
use Magento\Framework\EntityManager\EntityManager;
use Aheadworks\Giftcard\Api\PoolRepositoryInterface;
use Aheadworks\Giftcard\Model\Giftcard\CodeGenerator;
use Aheadworks\Giftcard\Model\Source\YesNo;
use Aheadworks\Giftcard\Api\Data\Pool\CodeInterface as PoolCodeInterface;
use Aheadworks\Giftcard\Api\Data\Pool\CodeInterfaceFactory as PoolCodeInterfaceFactory;
use Aheadworks\Giftcard\Model\Import\PoolCode as ImportPoolCode;
use Aheadworks\Giftcard\Api\Data\CodeGenerationSettingsInterface;
use Aheadworks\Giftcard\Api\Data\CodeGenerationSettingsInterfaceFactory;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Api\SortOrderBuilder;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class PoolService
 *
 * @package Aheadworks\Giftcard\Model\Service
 */
class PoolService implements PoolManagementInterface
{
    /**
     * CodeGenerator
     */
    private $codeGenerator;

    /**
     * PoolCodeRepositoryInterface
     */
    private $poolCodeRepository;

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * PoolRepositoryInterface
     */
    private $poolRepository;

    /**
     * PoolCodeInterfaceFactory
     */
    private $poolCodeFactory;

    /**
     * @var CodeGenerationSettingsInterfaceFactory
     */
    private $codeGenerationSettingsFactory;

    /**
     * ImportPoolCode
     */
    private $importPoolCode;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var SortOrderBuilder
     */
    private $sortOrderBuilder;

    /**
     * @param CodeGenerator $codeGenerator
     * @param PoolCodeRepositoryInterface $poolCodeRepository
     * @param EntityManager $entityManager
     * @param PoolRepositoryInterface $poolRepository
     * @param PoolCodeInterfaceFactory $poolCodeFactory
     * @param CodeGenerationSettingsInterfaceFactory $codeGenerationSettingsFactory
     * @param ImportPoolCode $importPoolCode
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param SortOrderBuilder $sortOrderBuilder
     */
    public function __construct(
        CodeGenerator $codeGenerator,
        PoolCodeRepositoryInterface $poolCodeRepository,
        EntityManager $entityManager,
        PoolRepositoryInterface $poolRepository,
        PoolCodeInterfaceFactory $poolCodeFactory,
        CodeGenerationSettingsInterfaceFactory $codeGenerationSettingsFactory,
        ImportPoolCode $importPoolCode,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        SortOrderBuilder $sortOrderBuilder
    ) {
        $this->codeGenerator = $codeGenerator;
        $this->poolCodeRepository = $poolCodeRepository;
        $this->entityManager = $entityManager;
        $this->poolRepository = $poolRepository;
        $this->poolCodeFactory = $poolCodeFactory;
        $this->codeGenerationSettingsFactory = $codeGenerationSettingsFactory;
        $this->importPoolCode = $importPoolCode;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->sortOrderBuilder = $sortOrderBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function generateCodesForPool($poolId, $qty)
    {
        $poolCodes = [];
        if (!$poolId || !$qty) {
            return $poolCodes;
        }

        $pool = $this->poolRepository->get($poolId);
        /** @var CodeGenerationSettingsInterface $codeGenerationSettings */
        $codeGenerationSettings = $this->codeGenerationSettingsFactory->create();
        $codeGenerationSettings
            ->setQty($qty)
            ->setLength($pool->getCodeLength())
            ->setFormat($pool->getCodeFormat())
            ->setPrefix($pool->getCodePrefix())
            ->setSuffix($pool->getCodeSuffix())
            ->setDelimiterAtEvery($pool->getCodeDelimiterAtEvery());

        $codes = $this->codeGenerator->generate($codeGenerationSettings, null);
        foreach ($codes as $code) {
            /** @var PoolCodeInterface $poolCode */
            $poolCode = $this->poolCodeFactory->create();
            $poolCode
                ->setPoolId($poolId)
                ->setCode($code)
                ->setUsed(YesNo::NO);
            $this->entityManager->save($poolCode);
            $poolCodes[] = $poolCode;
        }
        return $poolCodes;
    }

    /**
     * {@inheritdoc}
     */
    public function importCodesToPool($poolId, $codesRawData)
    {
        $poolCodes = [];
        if (!$poolId || !$codesRawData) {
            return $poolCodes;
        }
        $poolCodes = $this->importPoolCode->process($codesRawData);
        foreach ($poolCodes as $poolCode) {
            /** @var PoolCodeInterface $poolCode */
            $poolCode->setPoolId($poolId);
            $this->entityManager->save($poolCode);
        }

        return $poolCodes;
    }

    /**
     * {@inheritdoc}
     */
    public function pullCodeFromPool($poolId, $generateNew = true)
    {
        $sortOrder = $this->sortOrderBuilder
            ->setField(PoolCodeInterface::ID)
            ->setDirection(SortOrder::SORT_ASC)
            ->create();
        $this->searchCriteriaBuilder
            ->addFilter(PoolCodeInterface::USED, false)
            ->addFilter(PoolCodeInterface::POOL_ID, $poolId)
            ->setCurrentPage(1)
            ->setPageSize(1)
            ->addSortOrder($sortOrder);
        $poolCodes = $this->poolCodeRepository
            ->getList($this->searchCriteriaBuilder->create())
            ->getItems();

        if (!count($poolCodes) && !$generateNew) {
            return null;
        }
        if (!count($poolCodes) && $generateNew) {
            try {
                $this->poolRepository->get($poolId);
            } catch (NoSuchEntityException $e) {
                return null;
            }
            $poolCodes = $this->generateCodesForPool($poolId, 1);
        }
        $poolCode = array_shift($poolCodes);
        $poolCode->setUsed(YesNo::YES);
        $this->entityManager->save($poolCode);

        return $poolCode->getCode();
    }
}
