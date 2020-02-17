<?php


namespace Forix\ImportHelper\Console\Command;

use Forix\ImportHelper\Model\Export\Export;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


use Magento\Framework\App\ObjectManager\ConfigLoader;
use Magento\Framework\App\ObjectManagerFactory;
use Magento\Framework\App\State;
use Magento\Store\Model\StoreManager;
use Magento\Framework\Setup\Option\TextConfigOption;
use Magento\ImportExport\Model\Import\ErrorProcessing\ProcessingErrorAggregatorInterface;
use Forix\ImportHelper\Model\Import\Adapter as ImportAdapter;
use Magento\Framework\App\Filesystem\DirectoryList;


class ForixProcess extends Command
{
    const INPUT_KEY_FILE = "file";
    const INPUT_KEY_FUNCTION = "func";
    const INPUT_KEY_ENTITY = 'entity';
    const INPUT_KEY_BEHAVIOR = 'behavior';
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var \Magento\ImportExport\Helper\Report
     */
    protected $reportHelper;

    /**
     * @var \Magento\ImportExport\Model\Report\ReportProcessorInterface
     */
    protected $reportProcessor;

    /**
     * @var \Magento\Framework\Filesystem\Directory\WriteInterface
     */
    protected $varDirectory;


    protected $_export;

    /**
     * Forix_process constructor.
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\ImportExport\Helper\Report $reportHelper
     * @param \Magento\ImportExport\Model\Report\ReportProcessorInterface $reportProcessor
     * @param \Magento\Framework\Filesystem $filesystem
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\ImportExport\Helper\Report $reportHelper,
        \Forix\ImportHelper\Model\Export\ExportFactory $export,
        \Magento\ImportExport\Model\Report\ReportProcessorInterface $reportProcessor,
        \Magento\Framework\Filesystem $filesystem
    )
    {
        $this->objectManager = $objectManager;
        $this->reportHelper = $reportHelper;
        $this->reportProcessor = $reportProcessor;
        $this->varDirectory = $filesystem->getDirectoryWrite(DirectoryList::VAR_DIR);
        $this->_export = $export;
        parent::__construct();
    }

    protected function doImport($output, $inputFile)
    {

        /**
         * @var $importModel \Forix\ImportHelper\Model\Import
         */
        $importModel = $this->objectManager->get('\Forix\ImportHelper\Model\Import');


        if (!$inputFile || !file_exists($importModel->getWorkingDir() . $inputFile)) {
            throw new \Exception('Import File Is Invalid. Please Upload CSV file To Directory: ' . $importModel->getWorkingDir());
        }
        \Forix\ImportHelper\Model\Import\RawData::$fileName = $inputFile;
        $bkSourceFile = $importModel->getWorkingDir() . 'bk_' . $inputFile;
        $importedFile = $importModel->getWorkingDir() . $inputFile;
        copy($importedFile, $bkSourceFile);
        $data = array(
            self::INPUT_KEY_ENTITY => 'import_helper',
            self::INPUT_KEY_BEHAVIOR => 'append',
            $importModel::FIELD_NAME_VALIDATION_STRATEGY => ProcessingErrorAggregatorInterface::VALIDATION_STRATEGY_SKIP_ERRORS,
            $importModel::FIELD_NAME_ALLOWED_ERROR_COUNT => 10,
            $importModel::FIELD_FIELD_SEPARATOR => ',',
            $importModel::FIELD_FIELD_MULTIPLE_VALUE_SEPARATOR => \Magento\ImportExport\Model\Import::DEFAULT_GLOBAL_MULTI_VALUE_SEPARATOR,
        );

        $output->writeln('<info>Starting validation...</info>');

        $importModel->setData($data);

        $source = ImportAdapter::findAdapterFor(
            $importedFile,
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
            foreach ($importModel->getErrorAggregator()->getAllErrors() as $error) {
                $output->writeln("<error>{$error->getErrorMessage()}</error>");
            }
            $output->writeln("<comment>Checked rows: {$importModel->getProcessedRowsCount()}, checked entities: {$importModel->getProcessedEntitiesCount()}, invalid rows: {$errorAggregator->getInvalidRowsCount()}, total errors: {$errorAggregator->getErrorsCount()}</comment>");
        }
    }


    protected function doExport($output)
    {
        $this->_export->create()->exports();
    }

    public static function assertNotEmpty($actual)
    {
        PHPUnit_Framework_Assert:
        assertNotEmpty($actual);
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(
        InputInterface $input,
        OutputInterface $output
    )
    {
        $this->setAreaCode();

        $registry = $this->objectManager->get('\Magento\Framework\Registry');
        $registry->register('isSecureArea', true);
        try {
            switch ($input->getOption(self::INPUT_KEY_FUNCTION)) {
                case 'import':
                    $this->doImport($output, $input->getOption(self::INPUT_KEY_FILE));
                    break;
                case 'export':
                    $this->doExport($output);
                    break;
            }
        } catch (\Exception $e) {
            $output->writeln("<error>{$e->getMessage()}</error>");
        }

    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName("forix:process");
        $this->setDescription("Forix Helper Process raw data files");
        $this->setDefinition([
            new TextConfigOption(self::INPUT_KEY_FUNCTION, TextConfigOption::FRONTEND_WIZARD_TEXT, 'forix-process-raw/function', "Function export/import"),
            new TextConfigOption(self::INPUT_KEY_FILE, TextConfigOption::FRONTEND_WIZARD_TEXT, 'forix-process-raw/file', "Data Source File (var/importexport/)")
        ]);
        parent::configure();
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
