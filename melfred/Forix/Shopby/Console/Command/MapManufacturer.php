<?php

namespace Forix\Shopby\Console\Command;

use Magento\Framework\Event\Manager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Magento\Framework\Console\Cli;
use Magento\Eav\Model\Config;
use Forix\AdvancedAttribute\Model\ResourceModel\Option\Collection;

class MapManufacturer extends Command
{
	const INPUT_KEY_NAME = 'name';
	const INPUT_KEY_DESCRIPTION = 'description';

	protected $_eavConfig;
	protected $_advCollection;

	public function __construct(
		Config $eavConfig,
		Collection $eavCollection
	)
	{
		$this->_eavConfig = $eavConfig;
		$this->_advCollection = $eavCollection;
		parent::__construct();
	}

	protected function configure() {
		$this->setName('forix:rigmodel:map');
		parent::configure();
	}

	public function execute(InputInterface $input, OutputInterface $output)
	{
		$RigModel         = $this->getAllRigModel();
		$outPut           = [];
		$rigArrVermeer     = [
			'D6x6',
			'D7x11A',
			'D7x11A S2',
			'D8x12',
			'D9x13',
			'D9x13 S2',
			'D9x13 S3',
			'D10x15',
			'D10x15 S3',
			'D16x20',
			'D16x20 S2',
			'D18x22',
			'D20x22 S2',
			'D20x22 S3',
			'D23x30 S3',
			'D24x26',
			'D24x33',
			'D24x40',
			'D24x40A',
			'D24x40 S2',
			'D24x40 S3',
			'D33x44',
			'D36x50',
			'D36x50 S2',
			'D36x50 DR S2',
			'D40x40',
			'D40x55 S3',
			'D40x55 DR S3',
			'D50x100',
			'D60x90',
			'D60x90 S3',
			'D80x100',
			'D80x100 S2',
			'D80x120',
			'D100x120',
			'D100x120 S2',
			'D100x140',
			'D100x140 S3',
			'D220x300',
			'D220x300 S3'
		];
		//------
		$rigDitchWitch = [
			'JT5',
			'JT520',
			'JT9',
			'JT920',
			'JT921',
			'JT920L',
			'JT922',
			'JT10',
			'JT1220 M1',
			'JT1720',
			'JT1720 M1',
			'JT20',
			'JT2020',
			'JT25',
			'JT2720',
			'JT2720 M1',
			'JT2720 AT',
			'JT3020',
			'JT30',
			'JT30 AT',
			'JT3020 AT',
			'JT40',
			'JT40 AT',
			'JT4020',
			'JT4020 M1',
			'JT4020 AT',
			'JT60',
			'JT60 AT',
			'JT7020',
			'JT7020 M1',
			'JT8020 M1',
			'JT100 M1',
			'JT100 AT',
		];
		// ----------------
		$rigAstecToro = [
			'DD65',
			'DD1215',
			'DD1416',
			'DD2024',
			'DD2226',
			'DD3238',
			'DD4045',
			'DD4050',
			'DD9014'
		];

		$rigAmericanAugers = [
			'DD4',
			'DD5',
			'DD6',
			'DD8',
			'DD10',
			'DD220T'
		];

        $rigArrVermeer = array_map('strtolower', $rigArrVermeer);
        $rigDitchWitch = array_map('strtolower', $rigDitchWitch);
        $rigAstecToro = array_map('strtolower', $rigAstecToro);
        $rigAmericanAugers = array_map('strtolower', $rigAmericanAugers);
		foreach ($RigModel as $_item)
		{

			if (in_array(strtolower($_item['label']), $rigArrVermeer)) {
				$outPut[$_item['value']] = 1014;
			}

			if (in_array(strtolower($_item['label']), $rigDitchWitch)) {
				$outPut[$_item['value']] = 1017;
			}

			if (in_array(strtolower($_item['label']), $rigAstecToro)) {
				$outPut[$_item['value']] = 1020;
			}
			if (in_array(strtolower($_item['label']), $rigAmericanAugers)) {
				$outPut[$_item['value']] = 1503;
			}
		}


		if (!empty($outPut)) {
			foreach ($outPut as $optionId => $manufacturerId) {
				$this->_advCollection->updateData(
					['mb_oem'=>$manufacturerId],
					$optionId);
			}
		}
	}


	protected function getAllRigModel()
	{
		$attribute = $this->_eavConfig->getAttribute('catalog_product', 'mb_rig_model');
		$options = $attribute->getSource()->getAllOptions();
		return $options;
	}


}