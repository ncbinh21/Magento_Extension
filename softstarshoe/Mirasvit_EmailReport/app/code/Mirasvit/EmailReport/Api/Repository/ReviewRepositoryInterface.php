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
use Mirasvit\EmailReport\Api\Data\ReviewInterface;

interface ReviewRepositoryInterface extends RepositoryInterface
{
    /**
     * Retrieve review.
     *
     * @param int $id
     *
     * @return ReviewInterface
     * @throws NoSuchEntityException
     */
    public function get($id);

    /**
     * Create or update an review.
     *
     * @param ReviewInterface|AbstractModel $review
     *
     * @return ReviewInterface
     */
    public function save(ReviewInterface $review);

    /**
     * Create an review only if it does not exist yet.
     *
     * @param ReviewInterface|AbstractModel $review
     *
     * @return ReviewInterface
     */
    public function saveIfNotExist(ReviewInterface $review);

    /**
     * Delete review.
     *
     * @param ReviewInterface $review
     *
     * @return bool true on success
     */
    public function delete(ReviewInterface $review);

    /**
     * Retrieve collection of reviews.
     *
     * @return \Mirasvit\EmailReport\Model\ResourceModel\Review\Collection
     */
    public function getCollection();

    /**
     * Create new review.
     *
     * @return ReviewInterface
     */
    public function create();
}
