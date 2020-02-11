<?php
/**
 * Created by Mercurial
 * Job Title: Magento Developer
 * Project Name: M2 - SoftStart Shoes
 */

namespace Forix\Custom\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateOrderNote extends Command
{
    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    protected $_connection;

    /**
     * @var \FishPig\WordPress\Model\ResourceModel\Post\Collection
     */
    protected $postCollection;

    public function __construct(
        \Magento\Framework\App\State $state,
        $name = null
    )
    {
        try {
            $state->setAreaCode('frontend');
        } catch (\Exception $e) {
            $state->setAreaCode('frontend');

        }
        parent::__construct($name);
    }

    /**
     *
     */
    public function configure()
    {
        $this->setName('forix:updateordernote');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("Start!!!");
        //$this->update();
        $output->writeln("End!!!");
    }

    /**
     * @return Void
     */
    public function update()
    {
        $flag = 0;
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of Object Manager
        $resource = $objectManager->create('\Magento\Framework\App\ResourceConnection');
        $connection = $resource->getConnection();
        $sql = $connection->select()
            ->from($resource->getTableName('sales_order_status_history'))
            ->where('entity_name=?', 'order')
            ->where('parent_id>?', '109063');
        $query = $connection->query($sql);
        while ($row = $query->fetch()) {
            $historyCollection = $objectManager->create('\Magento\Sales\Model\ResourceModel\Order\Status\History\Collection');
            $historyItem = $historyCollection->addFieldToFilter('parent_id', $row['parent_id'])
                ->addFieldToFilter('is_customer_notified', null)->getFirstItem();
            print_r($row);
            echo('------------------------------------------------------------');
            if ($flag < $row['parent_id']) {
                $flag = $row['parent_id'];
                if (!$historyItem->getId()) {
                    //$row['comment'] = str_replace('"', '\"', $row['comment']);
                    $addData = 'INSERT INTO sales_order_status_history (parent_id, is_customer_notified, is_visible_on_front, comment ,status, created_at, entity_name, is_order_note) VALUES ("' . $row['parent_id'] . '","0","1","","' . $row['status'] . '","' . $row['created_at'] . '","order","1")';
                    $resourceData = $objectManager->create('\Magento\Framework\App\ResourceConnection');
                    $connectionData = $resourceData->getConnection();
                    $connectionData->query($addData);
                }
            }
        }
    }
}