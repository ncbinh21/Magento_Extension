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
use Mirasvit\EmailReport\Api\Data\OpenInterface;

interface OpenRepositoryInterface extends RepositoryInterface
{
    /**
     * Retrieve open.
     *
     * @param int $id
     *
     * @return OpenInterface
     * @throws NoSuchEntityException
     */
    public function get($id);

    /**
     * Create or update an open.
     *
     * @param OpenInterface|AbstractModel $open
     *
     * @return OpenInterface
     */
    public function save(OpenInterface $open);

    /**
     * Create an open only if it does not exist yet.
     *
     * @param OpenInterface|AbstractModel $open
     *
     * @return OpenInterface
     */
    public function saveIfNotExist(OpenInterface $open);

    /**
     * Delete open.
     *
     * @param OpenInterface $open
     *
     * @return bool true on success
     */
    public function delete(OpenInterface $open);

    /**
     * Retrieve collection of opens.
     *
     * @return \Mirasvit\EmailReport\Model\ResourceModel\Open\Collection
     */
    public function getCollection();

    /**
     * Create new open.
     *
     * @return OpenInterface
     */
    public function create();
}
