<?php

namespace Forix\Api\Model\ResourceModel\CustomerQueue;

class Collection extends \Forix\Payment\Model\ResourceModel\CustomerQueue\Collection
{

    public function insertData($data)
    {
        $this->getConnection()->beginTransaction();
        $this->getConnection()->insert($this->getConnection()->getTableName("forix_payment_customerqueue"), $data);
        $this->getConnection()->commit();
    }

    public function updateData($data, $customer_no)
    {
        $this->getConnection()->beginTransaction();
        $this->getConnection()->update($this->getConnection()->getTableName('forix_payment_customerqueue'), $data,
            [
                'customer_no = ?' => $customer_no
            ]
        );
        $this->getConnection()->commit();
    }

    public function selectDataByEmail($email)
    {
        $select = $this->getConnection()
            ->select()
            ->from(["customerqueue" => $this->getConnection()->getTableName("forix_payment_customerqueue")])
            ->where("customerqueue.customer_email = (?)", $email);
        return $this->getConnection()->fetchRow($select);
    }
}