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
 * @package   mirasvit/module-email-report
 * @version   1.0.5
 * @copyright Copyright (C) 2017 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\EmailReport\Api\Repository;


use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Model\AbstractModel;
use Mirasvit\EmailReport\Api\Data\ClickInterface;

interface ClickRepositoryInterface extends RepositoryInterface
{
    /**
     * Retrieve click.
     *
     * @param int $id
     *
     * @return ClickInterface
     * @throws NoSuchEntityException
     */
    public function get($id);

    /**
     * Create or update an click.
     *
     * @param ClickInterface|AbstractModel $click
     *
     * @return ClickInterface
     */
    public function save(ClickInterface $click);

    /**
     * Create an click only if it does not exist yet.
     *
     * @param ClickInterface|AbstractModel $click
     *
     * @return ClickInterface
     */
    public function saveIfNotExist(ClickInterface $click);

    /**
     * Delete click.
     *
     * @param ClickInterface $click
     *
     * @return bool true on success
     */
    public function delete(ClickInterface $click);

    /**
     * Retrieve collection of clicks.
     *
     * @return \Mirasvit\EmailReport\Model\ResourceModel\Click\Collection
     */
    public function getCollection();

    /**
     * Create new click.
     *
     * @return ClickInterface
     */
    public function create();
}