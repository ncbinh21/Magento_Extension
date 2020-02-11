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
use Mirasvit\EmailReport\Api\Data\EmailInterface;

interface EmailRepositoryInterface extends RepositoryInterface
{
    /**
     * Retrieve email.
     *
     * @param int $id
     *
     * @return EmailInterface
     * @throws NoSuchEntityException
     */
    public function get($id);

    /**
     * Create or update an email.
     *
     * @param EmailInterface|AbstractModel $email
     *
     * @return EmailInterface
     */
    public function save(EmailInterface $email);

    /**
     * Delete email.
     *
     * @param EmailInterface $email
     *
     * @return bool true on success
     */
    public function delete(EmailInterface $email);

    /**
     * Retrieve collection of emails.
     *
     * @return \Mirasvit\EmailReport\Model\ResourceModel\Email\Collection
     */
    public function getCollection();

    /**
     * Create new email.
     *
     * @return EmailInterface
     */
    public function create();
}
