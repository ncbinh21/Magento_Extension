<?php
/**
 * Created by Nick Howard.
 * Job Title: Magento Developer
 * Project: Bigshoes
 */

namespace Forix\CatalogImport\Console\Command;

use Magento\Framework\App\ObjectManager\ConfigLoader;
use Magento\Framework\App\ObjectManagerFactory;
use Magento\Framework\App\State;
use Magento\Store\Model\StoreManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Magento\Framework\Setup\Option\TextConfigOption;
use Symfony\Component\Console\Output\OutputInterface;
use Magento\ImportExport\Model\Import\ErrorProcessing\ProcessingErrorAggregatorInterface;
use \Forix\CatalogImport\Model\Import\Adapter as ImportAdapter;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\ImportExport\Model\History as ModelHistory;


class Import extends Command
{
    const INPUT_KEY_FILE = 'file';
    const INPUT_KEY_ENTITY = 'entity';
    const INPUT_KEY_BEHAVIOR = 'behavior';
    const INPUT_KEY_BASED = 'based';
    protected $objectManager;
    protected $historyModel;
    protected $reportHelper;
    protected $reportProcessor;
    protected $varDirectory;

    /**
     * Constructor
     *
     * @param ObjectManagerFactory $objectManagerFactory
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        //\Magento\ImportExport\Model\History $historyModel,
        \Magento\ImportExport\Helper\Report $reportHelper,
        \Magento\ImportExport\Model\Report\ReportProcessorInterface $reportProcessor,
        \Magento\Framework\Filesystem $filesystem
    )
    {
        $this->objectManager = $objectManager;
        //$this->historyModel = $historyModel;
        $this->reportHelper = $reportHelper;
        $this->reportProcessor = $reportProcessor;
        $this->varDirectory = $filesystem->getDirectoryWrite(DirectoryList::VAR_DIR);
        parent::__construct();
    }

    protected function configure()
    {
        $options = [
            new TextConfigOption(
                self::INPUT_KEY_ENTITY,
                TextConfigOption::FRONTEND_WIZARD_TEXT,
                'forix-command-import/entity',
                'Entity Type'
            ),
            new TextConfigOption(
                self::INPUT_KEY_BEHAVIOR,
                TextConfigOption::FRONTEND_WIZARD_TEXT,
                'forix-command-import/behavior',
                'Import Behavior'
            ),
            new TextConfigOption(
                self::INPUT_KEY_FILE,
                TextConfigOption::FRONTEND_WIZARD_TEXT,
                'forix-command-import/file',
                'File To Import'
            ),
            new TextConfigOption(
                self::INPUT_KEY_BASED,
                TextConfigOption::FRONTEND_WIZARD_TEXT,
                'forix-command-import/based',
                'Based on entity'
            )

        ];
        $this->setName('forix:import')
            ->setDescription('Import data via command line')
            ->setDefinition($options);
        parent::configure();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @throws \Magento\Framework\Exception\LocalizedException
     */

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $this->setAreaCode();
            /**
             * @var \Magento\Framework\Registry
             */
            $registry = $this->objectManager->get('\Magento\Framework\Registry');
            $registry->register('isSecureArea', true);

            $importModel = $this->objectManager->get('\Forix\CatalogImport\Model\Import');
            $behaviors = $importModel->getEntityBehaviors();
            $entityTypes = array_keys($behaviors);
            if (!$input->getOption(self::INPUT_KEY_ENTITY) || !in_array($input->getOption(self::INPUT_KEY_ENTITY), $entityTypes)) {
                throw new \Exception('Invalid Entity Type. Valid Types: ' . implode(', ', $entityTypes));
            }

            $validBehaviors = array_keys($this->objectManager->get($behaviors[$input->getOption(self::INPUT_KEY_ENTITY)]['token'])->toArray());

            if (!$input->getOption(self::INPUT_KEY_BEHAVIOR) || !in_array($input->getOption(self::INPUT_KEY_BEHAVIOR), $validBehaviors)) {
                throw new \Exception('Invalid Behavior. Valid Behaviors: ' . implode(', ', $validBehaviors));
            }

            if (!$input->getOption(self::INPUT_KEY_FILE) || !file_exists($importModel->getWorkingDir() . $input->getOption(self::INPUT_KEY_FILE))) {
                throw new \Exception('Import File Is Invalid. Please Upload CSV file To Directory: ' . $importModel->getWorkingDir());
            }

            $sourceFile = $importModel->getWorkingDir() . $input->getOption(self::INPUT_KEY_ENTITY) . '.csv';
            $importedFile = $importModel->getWorkingDir() . $input->getOption(self::INPUT_KEY_FILE);
            if (strtolower($input->getOption(self::INPUT_KEY_FILE)) != $input->getOption(self::INPUT_KEY_ENTITY) . '.csv') {
                copy($importedFile, $sourceFile);
            }

            $data = array(
                self::INPUT_KEY_ENTITY => $input->getOption(self::INPUT_KEY_BASED)?:$input->getOption(self::INPUT_KEY_ENTITY),
                'based_entity' => $input->getOption(self::INPUT_KEY_ENTITY),
                self::INPUT_KEY_BEHAVIOR => $input->getOption(self::INPUT_KEY_BEHAVIOR),
                $importModel::FIELD_NAME_VALIDATION_STRATEGY => ProcessingErrorAggregatorInterface::VALIDATION_STRATEGY_SKIP_ERRORS,
                $importModel::FIELD_NAME_ALLOWED_ERROR_COUNT => 10,
                $importModel::FIELD_FIELD_SEPARATOR => ',',
                $importModel::FIELD_FIELD_MULTIPLE_VALUE_SEPARATOR => \Magento\ImportExport\Model\Import::DEFAULT_GLOBAL_MULTI_VALUE_SEPARATOR,
            );

            $output->writeln('<info>Starting validation...</info>');

            $importModel->setData($data);

            $source = ImportAdapter::findAdapterFor(
                $sourceFile,
                $this->objectManager->create('Magento\Framework\Filesystem')
                    ->getDirectoryWrite(DirectoryList::ROOT),
                $data[$importModel::FIELD_FIELD_SEPARATOR]
            );
            $validationResult = $importModel->validateSource($source);

            if (!$importModel->getProcessedRowsCount()) {
                if (!$importModel->getErrorAggregator()->getErrorsCount()) {
                    $output->writeln('<error>This file is empty. Please try another one.</error>');
                } else {
                    foreach ($importModel->getErrorAggregator()->getAllErrors() as $error) {
                        $output->writeln("<error>{$error->getErrorMessage()}</error>");
                    }
                }
            } else {
                $errorAggregator = $importModel->getErrorAggregator();
                if (!$validationResult) {
                    $output->writeln('<error>Data validation is failed. Please fix errors and re-upload the file..</error>');
                    $countError = 0;
                    foreach ($errorAggregator->getRowsGroupedByErrorCode() as $errorMessage => $rows) {
                        $output->writeln('<error>' . ++$countError . '. ' . $errorMessage . ' in rows: ' . implode(', ', $rows) . '</error>');
                    }
                    //$this->createErrorReport($errorAggregator);
                    $output->writeln("<comment>Download full report: {$this->createErrorReport($importedFile,$errorAggregator)}</comment>");
                } else {
                    if ($importModel->isImportAllowed()) {
                        $output->writeln('<info>File is valid! Starting import process...</info>');
                        $importModel->importSource();
                        $errorAggregator = $importModel->getErrorAggregator();
                        if ($errorAggregator->hasToBeTerminated()) {
                            $output->writeln('<error>Maximum error count has been reached or system error is occurred!</error>');
                            $countError = 0;
                            foreach ($errorAggregator->getRowsGroupedByErrorCode() as $errorMessage => $rows) {
                                $output->writeln('<error>' . ++$countError . '. ' . $errorMessage . ' in rows: ' . implode(', ', $rows) . '</error>');
                            }
                        } else {
                            $importModel->invalidateIndex();
                            $countError = 0;
                            foreach ($errorAggregator->getRowsGroupedByErrorCode() as $errorMessage => $rows) {
                                $output->writeln('<error>' . ++$countError . '. ' . $errorMessage . ' in rows: ' . implode(', ', $rows) . '</error>');
                            }
                            $output->writeln('<info>Import successfully done</info>');
                        }
                    } else {
                        $output->writeln('<error>The file is valid, but we can\'t import it for some reason.</error>');
                    }
                }
                $output->writeln("<comment>Checked rows: {$importModel->getProcessedRowsCount()}, checked entities: {$importModel->getProcessedEntitiesCount()}, invalid rows: {$errorAggregator->getInvalidRowsCount()}, total errors: {$errorAggregator->getErrorsCount()}</comment>");
            }
        } catch (Exception $e) {
            $output->writeln("<error>{$e->getMessage()}</error>");
        }
    }

    private function setAreaCode()
    {
        $areaCode = 'adminhtml';
        /** @var \Magento\Framework\App\State $appState */
        $appState = $this->objectManager->get('Magento\Framework\App\State');
        /*if($appState->getAreaCode()){
            return $this;
        }*/
        $appState->setAreaCode($areaCode);
        /** @var \Magento\Framework\ObjectManager\ConfigLoaderInterface $configLoader */
        $configLoader = $this->objectManager->get('Magento\Framework\ObjectManager\ConfigLoaderInterface');
        $this->objectManager->configure($configLoader->load($areaCode));
    }

    protected function createErrorReport($importedFile, ProcessingErrorAggregatorInterface $errorAggregator)
    {
        $writeOnlyErrorItems = false;
        $fileName = $this->reportProcessor->createReport($importedFile, $errorAggregator, $writeOnlyErrorItems);
        return $this->reportHelper->getReportAbsolutePath($fileName);
    }
}