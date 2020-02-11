<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Makarovsoft\Notesoncustomers\Console\Command;

use League\CLImate\TerminalObject\Dynamic\Input;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

use Magento\Customer\Model\CustomerFactory;
use Makarovsoft\Notesoncustomers\Model\NotesFactory;

/**
 * Class GreetingCommand
 */
class ImportCustomers extends Command
{
    /**
     * Name argument
     */
    const PATH_TO_FILES = 'path';
    const PATH_TO_CSV = 'csv';
    const FILES_STORE_ID = 'store_id';

    protected $customerFactory;
    protected $notesFactory;
    protected $fileModel;

    public function __construct(
        CustomerFactory $factory,
        NotesFactory $notesFactory
    )
    {
        $this->customerFactory = $factory;
        $this->notesFactory = $notesFactory;

        return parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('mas:cnotes:import')
            ->setDescription('Import notes on customers from CSV file. First column is customer email, second - note')
            ->setDefinition([
                new InputArgument(
                    self::PATH_TO_CSV,
                    InputArgument::REQUIRED,
                    'Absolute Path To CSV'
                ),
            ]);

        parent::configure();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $csvPath = $input->getArgument(self::PATH_TO_CSV);

        if (!file_exists($csvPath)) {
            new RuntimeException("CSV file $csvPath does not exists");
        }



        $csv = [];
        $skus = [];
        if (($handle = fopen($csvPath, "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                $csv[] = $data;
                $skus[] = $data[0];
            }
            fclose($handle);
        }

        $insert = [];
        $notFoundCustomers = [];

        $customers = $this->customerFactory->create()->getCollection()->addFieldToFilter('email', ['in' => $skus]);

        $customersIds = [];

        /* @var $customer \Magento\Customer\Model\Customer */
        foreach ($customers as $customer) {
            $customersIds[$customer->getEmail()] = $customer->getId();
        }


        foreach ($csv as $row) {
            if (isset($row[0]) && isset($customersIds[trim($row[0])])) {
                $insert[$row[0]][] = trim($row[1]);
            } else {
                $notFoundCustomers[] = $row[0];
            }
        }


        /*
         * Insert file
         */
        foreach ($insert as $email => $note) {
            foreach ($note as $n) {
                $multiple[] = [
                    'customer_id' => $customersIds[$email],
                    'note' => $n,
                    'visible' => 0
                ];
            }
        }

        $table = $customers->getConnection()->getTableName('ams_notesoncustomers_notes');
        $customers->getConnection()->insertMultiple($table, $multiple);

        echo (__('INFO: %1 Notes have been added and attached to %2 customers', count($multiple), count($customersIds)));

        if (count($notFoundCustomers) > 0) {
            echo (__(PHP_EOL . 'WARNING: The following customers does not exists: %1', implode(PHP_EOL, $notFoundCustomers)));
        }

    }
}