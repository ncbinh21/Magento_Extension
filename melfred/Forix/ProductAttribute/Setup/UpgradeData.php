<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Forix\ProductAttribute\Setup;

use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Eav\Model\Entity\TypeFactory;
use Magento\Eav\Model\Entity\Attribute\SetFactory;
use Magento\Eav\Model\AttributeSetManagement;
use Magento\Eav\Model\AttributeManagement;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Catalog\Model\Product\Type;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute as eavAttribute;

/**
 * @codeCoverageIgnore
 */
class UpgradeData implements UpgradeDataInterface
{
    private $eavSetupFactory;
    /**
     * Eav Entity Type Factory
     *
     * @var TypeFactory
     */
    private $eavTypeFactory;
    /**
     * Attribute Set Factory
     *
     * @var SetFactory
     */
    private $attributeSetFactory;
    /**
     * Attribute Set Management
     *
     * @var AttributeSetManagement
     */
    private $attributeSetManagement;
    /**
     * Attribute Management
     *
     * @var AttributeManagement
     */
    private $attributeManagement;
    /**
     * Installed Attribute Sets
     *
     * @var array
     */
    private $installedAttributeSets;

    /**
     * Init
     *
     * @param EavSetupFactory $eavSetupFactory
     */

    public function __construct(
        EavSetupFactory $eavSetupFactory,
        TypeFactory $eavTypeFactory,
        SetFactory $attributeSetFactory,
        AttributeSetManagement $attributeSetManagement,
        AttributeManagement $attributeManagement,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory $attrOptionCollectionFactory
    )
    {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->eavTypeFactory = $eavTypeFactory;
        $this->attributeSetFactory = $attributeSetFactory;
        $this->attributeSetManagement = $attributeSetManagement;
        $this->attributeManagement = $attributeManagement;
    }

    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        /**
         * @var $eavSetup \Magento\Eav\Setup\EavSetup
         */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
        $groupname = 'MelfredBorzall';
        if (version_compare($context->getVersion(), '0.0.7', '<')) {


            $entityTypeCode = 'catalog_product';
            $entityType = $this->eavTypeFactory->create()->loadByCode($entityTypeCode);

            $attributes = [
                'mb_adapter_gender' => [
                    'group' => $groupname,
                    'type' => 'int',
                    'label' => 'Adapter Gender',
                    'input' => 'select',
                    'filterable' => true, //allow filter in layer navigation
                    'option' => [
                        'values' => [
                            0 => 'Box',
                            1 => 'Pin',
                            2 => 'Eye',
                        ]
                    ],
                ],

                'mb_adapter_option' => [
                    'group' => $groupname,
                    'type' => 'int',
                    'label' => 'Adapter Option',
                    'input' => 'select',
                    'filterable' => true, //allow filter in layer navigation
                    'option' => [
                        'values' => [
                            0 => 'Standard',
                            1 => 'Mud Boost',
                        ]
                    ],
                ],

                'mb_adapter_type' => [
                    'group' => $groupname,
                    'type' => 'int',
                    'label' => 'Adapter Type',
                    'input' => 'select',
                    'filterable' => false, //allow filter in layer navigation
                    'option' => [
                        'values' => [
                            0 => 'Standard',
                            1 => 'Mud Boost',
                        ]
                    ],
                ],

                'mb_auntie_lube_size' => [
                    'group' => $groupname,
                    'type' => 'int',
                    'label' => 'Auntie C\'s Thread Lube Sizes',
                    'input' => 'select',
                    'filterable' => true, //allow filter in layer navigation
                    'option' => [
                        'values' => [
                            0 => '20oz Tube',
                            1 => '1 Gallon Pail',
                            2 => '2 Gallon Pail',
                            3 => '5 Gallon Pail',
                        ]
                    ],
                ],

                'mb_auntie_lube_type' => [
                    'group' => $groupname,
                    'type' => 'int',
                    'label' => 'Auntie C\'s Thread Lube Types',
                    'input' => 'select',
                    'filterable' => true, //allow filter in layer navigation
                    'option' => [
                        'values' => [
                            0 => 'Standard',
                            1 => 'Arctic Grade',
                        ]
                    ],
                ],

                'mb_bitcutting_size' => [
                    'group' => $groupname,
                    'type' => 'int',
                    'label' => 'Bit Cutting Size',
                    'input' => 'select',
                    'filterable' => false, //allow filter in layer navigation
                    'option' => [
                        'values' => [
                            0 => '3.5"',
                            1 => '4"',
                            2 => '4.25"',
                            3 => '5"',
                            4 => '5.5"',
                            5 => '6"',
                            6 => '6.5"',
                        ]
                    ],
                ],

                'mb_bit_thread' => [
                    'group' => $groupname,
                    'type' => 'int',
                    'label' => 'Bit Thread',
                    'input' => 'select',
                    'filterable' => false, //allow filter in layer navigation
                    'option' => [
                        'values' => [
                            0 => '2"IF',
                            1 => '2-3/8"REG',
                            2 => '2-7/8"REG',
                            3 => '3-1/2"REG',
                        ]
                    ],
                ],

                'mb_blade_bolt_pattern' => [
                    'group' => $groupname,
                    'type' => 'int',
                    'label' => 'Blade Bolt Patterns',
                    'input' => 'select',
                    'filterable' => false, //allow filter in layer navigation
                    'option' => [
                        'values' => [
                            0 => '3, 12x20M bolts',
                            2 => '5, 3/8x3/4 bolts',
                            3 => '6, 16x35M bolts',
                            4 => '6, 20x40M bolts',
                            5 => '6, 1/2x1 bolts',
                            6 => '6, 5/8x1-1/4 bolts',
                            7 => '6, 3/4x1-1/2 bolts',
                        ]
                    ],
                ],

                'mb_blade_cutter_option' => [
                    'group' => $groupname,
                    'type' => 'int',
                    'label' => 'Blade Cutter Option',
                    'input' => 'select',
                    'filterable' => true, //allow filter in layer navigation
                    'option' => [
                        'values' => [
                            0 => 'Aggressive',
                            1 => 'Dome',
                        ]
                    ],
                ],

                'mb_blade_cutting_size' => [
                    'group' => $groupname,
                    'type' => 'int',
                    'label' => 'Blade Cutting Size',
                    'input' => 'select',
                    'filterable' => true, //allow filter in layer navigation
                    'option' => [
                        'values' => [
                            0 => '2.5"',
                            1 => '3"',
                            2 => '3.5"',
                            3 => '4"',
                            4 => '4-1/2"',
                            5 => '5"',
                            6 => '5-1/2"',
                            7 => '7-1/2"',
                            8 => '9"',
                        ]
                    ],
                ],

                'mb_break_connectorpins_type' => [
                    'group' => $groupname,
                    'type' => 'int',
                    'label' => 'Break-Away Connector Pins Types',
                    'input' => 'select',
                    'filterable' => true, //allow filter in layer navigation
                    'option' => [
                        'values' => [
                            1 => 'Light-Duty',
                            2 => 'Heavy-Duty',
                        ]
                    ],
                ],

                'mb_break_connector_type' => [
                    'group' => $groupname,
                    'type' => 'int',
                    'label' => 'Break-Away Connector Types',
                    'input' => 'select',
                    'filterable' => true, //allow filter in layer navigation
                    'option' => [
                        'values' => [
                            1 => 'Light-Duty',
                            2 => 'Heavy-Duty',
                            3 => 'Light-Duty Swivel',
                            4 => 'Mini Swivel',
                        ]
                    ],
                ],

                'mb_breakout_jaw' => [
                    'group' => $groupname,
                    'type' => 'int',
                    'label' => 'Breakout Jaws',
                    'input' => 'select',
                    'filterable' => 0, //allow filter in layer navigation
                    'option' => [
                        'values' => [
                            1 => 'Cylinder Side',
                            2 => 'Stationary Side',
                            3 => 'Inserts',
                        ]
                    ],
                ],

                'mb_digitrak_locator' => [
                    'group' => $groupname,
                    'type' => 'int',
                    'label' => 'Digitrak Locator Models',
                    'input' => 'select',
                    'filterable' => true, //allow filter in layer navigation
                    'option' => [
                        'values' => [
                            1 => 'Falcon',
                            2 => 'F5',
                            3 => 'F2',
                            4 => 'SE',
                        ]
                    ],
                ],

                'mb_digitrak_falcon_locator' => [
                    'group' => $groupname,
                    'type' => 'int',
                    'label' => 'Digitrak Falcon Locators',
                    'input' => 'select',
                    'filterable' => true, //allow filter in layer navigation
                    'option' => [
                        'values' => [
                            1 => 'System',
                            2 => 'Receiver',
                            3 => 'Remote Display',
                            4 => 'Charger',
                            5 => 'Transmitter',
                            6 => 'Battery',
                            7 => 'Transmitter Battery',
                            8 => 'GPS',
                            9 => 'Software',
                        ]
                    ],
                ],

                'mb_digitrak_falcon_model' => [
                    'group' => $groupname,
                    'type' => 'int',
                    'label' => 'Digitrak Falcon Models',
                    'input' => 'select',
                    'filterable' => true, //allow filter in layer navigation
                    'option' => [
                        'values' => [
                            1 => 'Falcon F1',
                            2 => 'Falcon F2',
                            3 => 'Falcon F5',
                        ]
                    ],
                ],

                'mb_digitrak_aurora' => [
                    'group' => $groupname,
                    'type' => 'int',
                    'label' => 'Digitrak Aurora Remote Displays',
                    'input' => 'select',
                    'filterable' => 0, //allow filter in layer navigation
                    'option' => [
                        'values' => [
                            1 => '8.4"',
                            2 => '10.4"',
                        ]
                    ],
                ],

                'mb_driver_chuck_type' => [
                    'group' => $groupname,
                    'type' => 'int',
                    'label' => 'Drive Chuck Types',
                    'input' => 'select',
                    'filterable' => true, //allow filter in layer navigation
                    'option' => [
                        'values' => [
                            1 => '1-piece',
                            2 => '2-piece',
                        ]
                    ],
                ],

                'mb_duct_puller_size' => [
                    'group' => $groupname,
                    'type' => 'int',
                    'label' => 'Duct Puller Sizes',
                    'input' => 'select',
                    'filterable' => true, //allow filter in layer navigation
                    'option' => [
                        'values' => [
                            1 => '3/4"',
                            2 => '1"',
                            3 => '1-1/2"',
                            4 => '2"',
                            5 => '2-1/2"',
                            6 => '3"',
                            7 => '4"',
                            8 => '5"',
                            9 => '6"',
                            10 => '8"',
                            11 => '10"',
                            12 => '12"',
                        ]
                    ],
                ],

                'mb_eagle_claw_teeth' => [
                    'group' => $groupname,
                    'type' => 'int',
                    'label' => 'Eagle Claw Carbide Teeth',
                    'input' => 'select',
                    'filterable' => true, //allow filter in layer navigation
                    'option' => [
                        'values' => [
                            1 => 'Chisel',
                            2 => 'Conical Aggressive',
                            3 => 'Conical Dome',
                            4 => 'Conical Aggressive Hardfaced',
                        ]
                    ],
                ],

                'mb_fastream_cutter_size' => [
                    'group' => $groupname,
                    'type' => 'int',
                    'label' => 'FastReam Cutter Block Cut Size',
                    'input' => 'select',
                    'filterable' => 0, //allow filter in layer navigation
                    'option' => [
                        'values' => [
                            1 => '4-1/2"',
                            2 => '6"',
                            3 => '8"',
                            4 => '10"',
                            5 => '12"',
                        ]
                    ],
                ],

                'mb_fastream_cutter_block' => [
                    'group' => $groupname,
                    'type' => 'int',
                    'label' => 'FastReam Cutter Block Housing Fit-Up',
                    'input' => 'select',
                    'filterable' => 0, //allow filter in layer navigation
                    'option' => [
                        'values' => [
                            1 => '2-1/2"',
                            2 => '2-3/4"',
                            3 => '3-1/4"',
                        ]
                    ],
                ],

                'mb_glove_size' => [
                    'group' => $groupname,
                    'type' => 'int',
                    'label' => 'Glove Size',
                    'input' => 'select',
                    'filterable' => true, //allow filter in layer navigation
                    'option' => [
                        'values' => [
                            1 => '8',
                            2 => '9',
                            3 => '10',
                            4 => '11',
                            5 => '12',
                            6 => '13',
                            7 => '14',
                        ]
                    ],
                ],

                'mb_ironfist_cutter' => [
                    'group' => $groupname,
                    'type' => 'int',
                    'label' => 'Iron Fist Cutter Blocks',
                    'input' => 'select',
                    'filterable' => 0, //allow filter in layer navigation
                    'option' => [
                        'values' => [
                            1 => 'Aggressive',
                            2 => 'Dome',
                        ]
                    ],
                ],

                'mb_manufacturer' => [
                    'group' => $groupname,
                    'type' => 'int',
                    'label' => 'Manufacturer',
                    'input' => 'select',
                    'filterable' => 1, //allow filter in layer navigation
                    'option' => [
                        'values' => [
                            1 => 'Vermeer',
                            2 => 'Ditch Witch',
                            3 => 'Astec/Toro',
                            4 => 'American Augers',
                            5 => 'Universal HDD',
                        ]
                    ],
                ],

                'mb_pitbull_blades_styles' => [
                    'group' => $groupname,
                    'type' => 'int',
                    'label' => 'PitBull Blades Styles',
                    'input' => 'select',
                    'filterable' => 0, //allow filter in layer navigation
                    'option' => [
                        'values' => [
                            1 => 'Red Diamond',
                            2 => 'Ultra Bit',
                            3 => 'Steep Taper Ultra Bit',
                        ]
                    ],
                ],

                'mb_pitbull_bolt_pattern' => [
                    'group' => $groupname,
                    'type' => 'int',
                    'label' => 'PitBull Bolt Patterns',
                    'input' => 'select',
                    'filterable' => 0, //allow filter in layer navigation
                    'option' => [
                        'values' => [
                            1 => '-14',
                            2 => '-13',
                        ]
                    ],
                ],

                'mb_pitbull_housing_size' => [
                    'group' => $groupname,
                    'type' => 'int',
                    'label' => 'PitBull Housing Sizes',
                    'input' => 'select',
                    'filterable' => 0, //allow filter in layer navigation
                    'option' => [
                        'values' => [
                            1 => '2-1/2"',
                            2 => '2-3/4"',
                            3 => '3-1/4"',
                        ]
                    ],
                ],

                'mb_pitbull_housings' => [
                    'group' => $groupname,
                    'type' => 'int',
                    'label' => 'PitBull Housings',
                    'input' => 'select',
                    'filterable' => 0, //allow filter in layer navigation
                    'option' => [
                        'values' => [
                            1 => '4925-HD3-14',
                            2 => '4927-HD3-14',
                            3 => '4625-HT3-13',
                            4 => '4627-HT3-13',
                            5 => '4632-HT3-13',
                            6 => '3825-HT3-06',
                            7 => '4925-HD3-14-FR',
                            8 => '4927-HD3-14-FR',
                            9 => '4625-HT3-13-FR',
                            10 => '4632-HT3-13-FR',
                            11 => '3825-HT3-06-FR',
                        ]
                    ],
                ],

                'mb_pullback_type' => [
                    'group' => $groupname,
                    'type' => 'int',
                    'label' => 'Pullback Type',
                    'input' => 'select',
                    'filterable' => 1, //allow filter in layer navigation
                    'option' => [
                        'values' => [
                            1 => 'Quick-Swivel',
                            2 => 'Quick Link',
                        ]
                    ],
                ],

                'mb_pullback_grip' => [
                    'group' => $groupname,
                    'type' => 'int',
                    'label' => 'Pulling Grips',
                    'input' => 'select',
                    'filterable' => 0, //allow filter in layer navigation
                    'option' => [
                        'values' => [
                            1 => 'Standard-Duty',
                            2 => 'Heavy-Duty',
                        ]
                    ],
                ],

                'mb_pulling_sling_cable' => [
                    'group' => $groupname,
                    'type' => 'int',
                    'label' => 'Pulling Sling Cable Diameter',
                    'input' => 'select',
                    'filterable' => 0, //allow filter in layer navigation
                    'option' => [
                        'values' => [
                            1 => '1/4"',
                            2 => '3/8"',
                            3 => '1/2"',
                        ]
                    ],
                ],

                'mb_pulling_sling_legs' => [
                    'group' => $groupname,
                    'type' => 'int',
                    'label' => 'Pulling Sling Legs',
                    'input' => 'select',
                    'filterable' => 0, //allow filter in layer navigation
                    'option' => [
                        'values' => [
                            1 => '2',
                            2 => '3',
                            3 => '4',
                            4 => '6',
                        ]
                    ],
                ],

                'mb_quick_disconnect' => [
                    'group' => $groupname,
                    'type' => 'int',
                    'label' => 'Quick Disconnect',
                    'input' => 'select',
                    'filterable' => 0, //allow filter in layer navigation
                    'option' => [
                        'values' => [
                            1 => 'Value',
                            2 => 'Deluxe',
                        ]
                    ],
                ],

                'mb_quick_disconnect_type' => [
                    'group' => $groupname,
                    'type' => 'int',
                    'label' => 'Quick Disconnect types',
                    'input' => 'select',
                    'filterable' => 1, //allow filter in layer navigation
                    'option' => [
                        'values' => [
                            1 => 'Low Profile',
                            2 => 'QuickFire',
                            5 => 'EZ-Connect',
                            3 => 'Hawkeye',
                            4 => 'SplinLok',
                        ]
                    ],
                ],

                'mb_reamer_back_thread' => [
                    'group' => $groupname,
                    'type' => 'int',
                    'label' => 'Reamer Back Thread',
                    'input' => 'select',
                    'filterable' => 0, //allow filter in layer navigation
                    'option' => [
                        'values' => [
                            1 => '2" IF',
                            2 => '2-3/8"REG',
                            5 => '2-7/8" IF',
                            3 => '3-1/2" IF',
                            4 => '4-1/2" IF',
                        ]
                    ],
                ],

                'mb_reamer_cutter_option' => [
                    'group' => $groupname,
                    'type' => 'int',
                    'label' => 'Reamer Cutter Option',
                    'input' => 'select',
                    'filterable' => 0, //allow filter in layer navigation
                    'option' => [
                        'values' => [
                            1 => 'Sharktooth',
                            2 => 'Conical Aggressive',
                            5 => 'Conical Dome',
                        ]
                    ],
                ],

                'mb_reamer_cutting_size' => [
                    'group' => $groupname,
                    'type' => 'int',
                    'label' => 'Reamer Cutting Size',
                    'input' => 'select',
                    'filterable' => 0, //allow filter in layer navigation
                    'option' => [
                        'values' => [
                            1 => '4"',
                            2 => '4-3/4"',
                            22 => '6"',
                            3 => '8"',
                            4 => '10"',
                            5 => '12"',
                            6 => '14"',
                            7 => '16"',
                            8 => '18"',
                            9 => '20"',
                            10 => '22"',
                            11 => '26"',
                            12 => '28"',
                            13 => '30"',
                            14 => '32"',
                            15 => '34"',
                            16 => '36"',
                            17 => '38"',
                            18 => '40"',
                            19 => '42"',
                            20 => '44"',
                            21 => '46"',
                            23 => '48"',
                        ]
                    ],
                ],

                'mb_reamer_front_thread' => [
                    'group' => $groupname,
                    'type' => 'int',
                    'label' => 'Reamer Front Thread',
                    'input' => 'select',
                    'filterable' => 0, //allow filter in layer navigation
                    'option' => [
                        'values' => [
                            1 => '2" IF',
                            2 => '2-3/8"REG',
                            3 => '2-7/8" IF',
                            4 => '3-1/2" IF',
                            5 => '4-1/2" IF',
                        ]
                    ],
                ],

                'mb_reamer_rear_connection' => [
                    'group' => $groupname,
                    'type' => 'int',
                    'label' => 'Reamer Rear Connection Options',
                    'input' => 'select',
                    'filterable' => 0, //allow filter in layer navigation
                    'option' => [
                        'values' => [
                            1 => 'Bolt-on Flange Swivel',
                            2 => 'Thread-in Swivel',
                            3 => 'Pulling Eye',
                        ]
                    ],
                ],

                'mb_reamer_shaft_size' => [
                    'group' => $groupname,
                    'type' => 'int',
                    'label' => 'Reamer Shaft Size',
                    'input' => 'select',
                    'filterable' => 0, //allow filter in layer navigation
                    'option' => [
                        'values' => [
                            1 => '2-3/4"',
                            2 => '3-1/4"',
                            3 => '4-1/4"',
                            4 => '4-7/8"',
                            5 => '6-3/8"',
                        ]
                    ],
                ],

                'mb_rig_model' => [
                    'group' => $groupname,
                    'type' => 'varchar',
                    'label' => 'Rig Models',
                    'input' => 'multiselect',
                    'filterable' => 1, //allow filter in layer navigation
                    'option' => [
                        'values' => [
                            1 => 'D6x6',
                            2 => 'D7x11A',
                            3 => 'D7x11A S2',
                            4 => 'D8x12',
                            5 => 'D9x13',
                            6 => 'D9x13 S2',
                            7 => 'D9x13 S3',
                            8 => 'D10x15',
                            9 => 'D10x15 S3',
                            10 => 'D16x20',
                            11 => 'D16x20 S2',
                            12 => 'D18x22',
                            13 => 'D20x22 S2',
                            14 => 'D20x22 S3',
                            15 => 'D23x30 S3',
                            16 => 'D24x26',
                            17 => 'D24x33',
                            18 => 'D24x40',
                            19 => 'D24x40A',
                            20 => 'D24x40 S2',
                            21 => 'D24x40 S3',
                            22 => 'D33x44',
                            23 => 'D36x50',
                            24 => 'D36x50 S3',
                            25 => 'D36x50 DR S2',
                            26 => 'D40x40',
                            27 => 'D40x55 S3',
                            28 => 'D40x55 DR S3',
                            29 => 'D50x100',
                            30 => 'D60x90',
                            31 => 'D60x90 S3',
                            32 => 'D80x100',
                            33 => 'D80x100 S2',
                            34 => 'D80x120',
                            35 => 'D100x120',
                            36 => 'D100x120 S2',
                            37 => 'D100x140',
                            38 => 'D100x140 S3',
                            39 => 'D220x300',
                            40 => 'D220x300 S3',
                            41 => 'JT5',
                            42 => 'JT520',
                            43 => 'JT920',
                            44 => 'JT921',
                            45 => 'JT920L',
                            46 => 'JT922',
                            47 => 'JT9',
                            48 => 'JT10',
                            49 => 'JT1220 M1',
                            50 => 'JT1720',
                            51 => 'JT1720 M1',
                            52 => 'JT20',
                            53 => 'JT2020',
                            54 => 'JT25',
                            55 => 'JT2720',
                            56 => 'JT2720 M1',
                            57 => 'JT2720 AT',
                            58 => 'JT3020',
                            59 => 'JT30',
                            60 => 'JT30 AT',
                            61 => 'JT3020 AT',
                            62 => 'JT40',
                            63 => 'JT40 AT',
                            64 => 'JT4020',
                            65 => 'JT4020 M1',
                            66 => 'JT4020 AT',
                            67 => 'JT60',
                            68 => 'JT60AT',
                            69 => 'JT7020',
                            70 => 'JT7020 M1',
                            71 => 'JT8020 M1',
                            72 => 'JT100 M1',
                            73 => 'JT100 AT',
                            74 => 'DD65',
                            75 => 'DD1215',
                            76 => 'DD1416',
                            77 => 'DD2024',
                            78 => 'DD2226',
                            79 => 'DD3238',
                            80 => 'DD4045',
                            81 => 'DD4050',
                            82 => 'DD9014',
                            83 => 'DD4',
                            84 => 'DD5',
                            85 => 'DD6',
                            86 => 'DD8',
                            87 => 'DD10',
                            88 => 'DD220T',
                        ]
                    ],
                ],

                'mb_shoe_size' => [
                    'group' => $groupname,
                    'type' => 'int',
                    'label' => 'Shoe Size',
                    'input' => 'select',
                    'filterable' => 1, //allow filter in layer navigation
                    'option' => [
                        'values' => [
                            1 => '7',
                            2 => '8',
                            3 => '9',
                            4 => '10',
                            5 => '11',
                            6 => '12',
                            7 => '13',
                            8 => '14',
                            9 => '15',
                            10 => '16',

                        ]
                    ],
                ],

                'mb_slide_collar' => [
                    'group' => $groupname,
                    'type' => 'int',
                    'label' => 'Slide Collars',
                    'input' => 'select',
                    'filterable' => 0, //allow filter in layer navigation
                    'option' => [
                        'values' => [
                            1 => 'Standard',
                            2 => 'Carbide Hardfacing',
                            3 => 'Carbide Studs',
                            4 => 'Carbide Hardfacing + Cutters',

                        ]
                    ],
                ],

                'mb_soil_type_best' => [
                    'group' => $groupname,
                    'type' => 'varchar',
                    'label' => 'Soil Type Best',
                    'input' => 'multiselect',
                    'filterable' => 1, //allow filter in layer navigation
                    'option' => [
                        'values' => [
                            1 => 'Dirt',
                            9 => 'Gravel',
                            2 => 'Sand',
                            3 => 'Clay',
                            4 => 'Shale',
                            5 => 'Caliche',
                            6 => 'Hardpan',
                            7 => 'Sandstone',
                            8 => 'Cobble',
                        ]
                    ],
                ],

                'mb_soil_type_better' => [
                    'group' => $groupname,
                    'type' => 'varchar',
                    'label' => 'Soil Type Better',
                    'input' => 'multiselect',
                    'filterable' => 0, //allow filter in layer navigation
                    'option' => [
                        'values' => [
                            1 => 'Dirt',
                            9 => 'Gravel',
                            2 => 'Sand',
                            3 => 'Clay',
                            4 => 'Shale',
                            5 => 'Caliche',
                            6 => 'Hardpan',
                            7 => 'Sandstone',
                            8 => 'Cobble',
                        ]
                    ],
                ],

                'mb_soil_type_good' => [
                    'group' => $groupname,
                    'type' => 'varchar',
                    'label' => 'Soil Type Good',
                    'input' => 'multiselect',
                    'filterable' => 0, //allow filter in layer navigation
                    'option' => [
                        'values' => [
                            1 => 'Dirt',
                            9 => 'Gravel',
                            2 => 'Sand',
                            3 => 'Clay',
                            4 => 'Shale',
                            5 => 'Caliche',
                            6 => 'Hardpan',
                            7 => 'Sandstone',
                            8 => 'Cobble',
                        ]
                    ],
                ],

                'mb_stabillizer_barrel' => [
                    'group' => $groupname,
                    'type' => 'int',
                    'label' => 'Stabillizer Barrel Options',
                    'input' => 'select',
                    'filterable' => 0, //allow filter in layer navigation
                    'option' => [
                        'values' => [
                            1 => 'Plain',
                            2 => 'Value',
                            3 => 'Deluxe',

                        ]
                    ],
                ],

                'mb_swivel_capacities' => [
                    'group' => $groupname,
                    'type' => 'int',
                    'label' => 'Swivel Capacities',
                    'input' => 'select',
                    'filterable' => 1, //allow filter in layer navigation
                    'option' => [
                        'values' => [
                            1 => '2-ton',
                            2 => '2-1/2 ton',
                            3 => '4-1/4 ton',
                            4 => '5 ton',
                            5 => '7-1/2 ton',
                            6 => '10-ton',
                            7 => '15-ton',
                            8 => '20-ton',
                            9 => '30-ton',
                            10 => '40-ton',
                            11 => '45-ton',
                            12 => '50-ton',
                            13 => '60-ton',
                            14 => '80-ton',
                            15 => '110-ton',
                            16 => '150-ton',
                            17 => '165-ton',
                            18 => '220-ton',
                            19 => '250-ton',
                            20 => '275-ton',
                            21 => '500-ton',

                        ]
                    ],
                ],

                'mb_swivel_connection' => [
                    'group' => $groupname,
                    'type' => 'int',
                    'label' => 'Swivel Connection',
                    'input' => 'select',
                    'filterable' => 0, //allow filter in layer navigation
                    'option' => [
                        'values' => [
                            1 => 'thread',
                            2 => 'pin',
                            3 => 'thread box',
                            4 => 'clevis',
                            5 => 'flange',

                        ]
                    ],
                ],

                'mb_swivel_thread' => [
                    'group' => $groupname,
                    'type' => 'int',
                    'label' => 'Swivel Thread',
                    'input' => 'select',
                    'filterable' => 0, //allow filter in layer navigation
                    'option' => [
                        'values' => [
                            1 => '2"IF',
                            2 => '2-3/8"Reg',
                            3 => '2-7/8"IF',
                            4 => '2-3/8"IF',
                            5 => '3-1/2"IF',
                            6 => '4-1/2"IF',
                            7 => '2-1/4"-12',
                            8 => '2-5/8"-16',
                            9 => '2-1/8"LP',

                        ]
                    ],
                ],

                'mb_swivel_types' => [
                    'group' => $groupname,
                    'type' => 'int',
                    'label' => 'Swivel Types',
                    'input' => 'select',
                    'filterable' => 1, //allow filter in layer navigation
                    'option' => [
                        'values' => [
                            1 => 'Value DUB',
                            2 => 'Deluxe DUB',
                        ]
                    ],
                ],

                'mb_thread_connection_type' => [
                    'group' => $groupname,
                    'type' => 'int',
                    'label' => 'Thread Connection Type',
                    'input' => 'select',
                    'filterable' => 1, //allow filter in layer navigation
                    'option' => [
                        'values' => [
                            1 => 'Drill rod',
                            2 => 'Quick Disconnect',
                        ]
                    ],
                ],

                'mb_transmitter_diameter' => [
                    'group' => $groupname,
                    'type' => 'int',
                    'label' => 'Transmitter Housing Diameter',
                    'input' => 'select',
                    'filterable' => 1, //allow filter in layer navigation
                    'option' => [
                        'values' => [
                            1 => '2"',
                            2 => '2-1/2"',
                            3 => '2-3/4"',
                            4 => '3"',
                            5 => '3-1/4"',
                            6 => '3-1/2"',
                            7 => '4-1/4"',
                            8 => '4-7/8"',
                            9 => '6-3/8"',
                        ]
                    ],
                ],

                'mb_transmitter_feature' => [
                    'group' => $groupname,
                    'type' => 'int',
                    'label' => 'Transmitter Housing Feature',
                    'input' => 'select',
                    'filterable' => 0, //allow filter in layer navigation
                    'option' => [
                        'values' => [
                            1 => 'Standard',
                            2 => 'FastReam',
                            3 => 'PitBull',
                            4 => 'FastReam+Pitbull',

                        ]
                    ],
                ],

                'mb_transmitter_front_connect' => [
                    'group' => $groupname,
                    'type' => 'int',
                    'label' => 'Transmitter Housing Front Connection',
                    'input' => 'select',
                    'filterable' => 0, //allow filter in layer navigation
                    'option' => [
                        'values' => [
                            1 => 'Thread-on',
                            2 => 'Bolt-on',
                        ]
                    ],
                ],

                'mb_transmitter_front_thread' => [
                    'group' => $groupname,
                    'type' => 'int',
                    'label' => 'Transmitter Housing Front Thread',
                    'input' => 'select',
                    'filterable' => 0, //allow filter in layer navigation
                    'option' => [
                        'values' => [
                            1 => '1.21-6',
                            2 => '2"IF',
                            3 => '2-3/8"Reg',
                            4 => '2-7/8"Reg',
                            5 => '3-1/2"Reg',
                            6 => '4-1/2"Reg',

                        ]
                    ],
                ],

                'mb_transmitter_rear_thread' => [
                    'group' => $groupname,
                    'type' => 'int',
                    'label' => 'Transmitter Housing Rear Thread',
                    'input' => 'select',
                    'filterable' => 0, //allow filter in layer navigation
                    'option' => [
                        'values' => [
                            1 => '1.21-6',
                            2 => '1.66"',
                            3 => 'FS200',
                            4 => '2" IF',
                            5 => '2-3/8"REG',
                            6 => '2-3/8" IF',
                            7 => '2-7/8" IF',
                            8 => '3-1/2" IF',
                            9 => '4-1/2" IF',

                        ]
                    ],
                ],

                'mb_transmitter_housing_type' => [
                    'group' => $groupname,
                    'type' => 'int',
                    'label' => 'Transmitter Housing Type',
                    'input' => 'select',
                    'filterable' => 0, //allow filter in layer navigation
                    'option' => [
                        'values' => [
                            1 => 'Side-load',
                            2 => 'End-Load (high-flow)',
                        ]
                    ],
                ],

                'mb_transmitter_type' => [
                    'group' => $groupname,
                    'type' => 'int',
                    'label' => 'Transmitter Type',
                    'input' => 'select',
                    'filterable' => 0, //allow filter in layer navigation
                    'option' => [
                        'values' => [
                            1 => 'Digitrak 15"',
                            2 => 'Digitrak 19"',
                            3 => 'Digitrak 15" cable',
                            4 => 'Digitrak 19" cable',
                            5 => 'Subsite 86BRP',
                            6 => 'Subsite 86BRV',
                        ]
                    ],
                ],

            ];

            try {
                foreach ($attributes as $attributeCode => $attributeInfo) {
                    $attributeExist = $eavSetup->getAttribute($entityType->getId(), $attributeCode);
                    if ($attributeExist) {
                        $eavSetup->removeAttribute(
                            $entityType->getId(), $attributeCode
                        );
                    }
                    if (!isset($attributeInfo['remove']) || $attributeInfo['remove'] != true) {
                        $eavSetup->addAttribute($entityType->getId(), $attributeCode,
                            array_merge(
                                [
                                    'backend' => '',
                                    'frontend' => '',
                                    'class' => '',
                                    'used_in_product_listing' => false,
                                    'system' => 0,
                                    'searchable' => true,
                                    'comparable' => false,
                                    'source' => '',
                                    'required' => false,
                                    'user_defined' => true,
                                    'default' => null,
                                    'visible_on_front' => true,
                                    'unique' => false,
                                    'apply_to' => 'simple,configurable,bundle',
                                    'visible' => true,
                                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,

                                ],
                                $attributeInfo
                            )
                        );
                    }
                }


            } catch (\Exception $e) {
                die ($e);
            }
        }


        if (version_compare($context->getVersion(), '1.1.0', '<')) {
            $entityTypeCode = 'catalog_product';
            $entityType = $this->eavTypeFactory->create()->loadByCode($entityTypeCode);
            $defaultSetId = $entityType->getDefaultAttributeSetId();

            $listAttributeset = [
                'Reamer',
                'FastBack',
                'Transmitter Housings',
                'Bits & Blades',
                'Mud Motors',
                'Quick-Disconnects',
                'Collars',
                'Swivels',
                'Pullers',
                'Drive Chucks & Sub-Savers',
                'Adapters',
                'Parts & Accessories',
                'Locators',
                'Auntie C\'s',
                'Jaws',
            ];
            $orders = 10;
            foreach ($listAttributeset as $attributeitem) {
                $data = [
                    'attribute_set_name' => $attributeitem,
                    'entity_type_id' => $entityType->getId(),
                    'sort_order' => $orders,
                ];

                $attributeSet = $this->attributeSetFactory->create();
                $attributeSet->setData($data);
                $this->attributeSetManagement->create($entityTypeCode, $attributeSet, $defaultSetId);
                $autosettingsTabName = "MB {$data['attribute_set_name']}";
                $eavSetup->addAttributeGroup($entityType->getId(), $attributeSet->getId(), $autosettingsTabName, $orders);
            }
        }


        if (version_compare($context->getVersion(), '1.1.2', '<')) {
            $entityTypeCode = 'catalog_product';
            $entityType = $this->eavTypeFactory->create()->loadByCode($entityTypeCode);

            $installedAttributeSets = $entityType->getAttributeSetCollection();
            try {
                $attrSetRel = [
                    'Default' => [
                        'mb_manufacturer',
                        'mb_blade_bolt_pattern',
                        'mb_reamer_cutter_option',
                        'mb_reamer_cutting_size',
                        'mb_reamer_front_thread',
                        'mb_reamer_back_thread',
                        'mb_reamer_shaft_size',
                        'mb_soil_type_best',
                        'mb_soil_type_good',
                        'mb_breakout_jaw',
                        'mb_glove_size',
                        'mb_shoe_size',
                        'mb_blade_cutting_size',
                        'mb_blade_cutter_option',
                        'mb_bitcutting_size',
                        'mb_quick_disconnect',
                        'mb_slide_collar',
                        'mb_transmitter_diameter',
                        'mb_transmitter_housing_type',
                        'mb_transmitter_front_connect',
                    ],
                    'Reamer' => [
                        'mb_manufacturer',
                        'mb_reamer_cutter_option',
                        'mb_reamer_cutting_size',
                        'mb_reamer_front_thread',
                        'mb_reamer_back_thread',
                        'mb_reamer_shaft_size',
                        'mb_soil_type_best',
                        'mb_soil_type_better',
                        'mb_soil_type_good',
                        'mb_reamer_rear_connection',
                        'mb_rig_model',
                        'mb_swivel_capacities',
                        'mb_swivel_types',
                        'mb_adapter_gender',
                    ],
                    'FastBack' => [
                        'mb_fastream_cutter_block',
                        'mb_fastream_cutter_size',
                        'mb_manufacturer',
                        'mb_blade_bolt_pattern',
                        'mb_soil_type_best',
                        'mb_soil_type_better',
                        'mb_soil_type_good',
                        'mb_blade_cutting_size',
                        'mb_blade_cutter_option',
                        'mb_bitcutting_size',
                        'mb_bit_thread',
                        'mb_transmitter_housing_type',
                        'mb_transmitter_diameter',
                        'mb_transmitter_front_connect',
                        'mb_transmitter_front_thread',
                        'mb_transmitter_rear_thread',
                        'mb_transmitter_type',
                        'mb_rig_model',
                        'mb_adapter_option',
                        'mb_transmitter_feature',
                        'mb_quick_disconnect',
                        'mb_adapter_gender',
                    ],
                    'Transmitter Housings' => [
                        'mb_manufacturer',
                        'mb_blade_bolt_pattern',
                        'mb_soil_type_best',
                        'mb_soil_type_better',
                        'mb_soil_type_good',
                        'mb_blade_cutting_size',
                        'mb_blade_cutter_option',
                        'mb_bitcutting_size',
                        'mb_bit_thread',
                        'mb_transmitter_housing_type',
                        'mb_transmitter_diameter',
                        'mb_transmitter_front_connect',
                        'mb_transmitter_front_thread',
                        'mb_transmitter_rear_thread',
                        'mb_transmitter_type',
                        'mb_rig_model',
                        'mb_adapter_option',
                        'mb_transmitter_feature',
                        'mb_quick_disconnect_type',
                        'mb_adapter_gender',
                        'mb_thread_connection_type',
                    ],
                    'Bits & Blades' => [
                        'mb_manufacturer',
                        'mb_blade_bolt_pattern',
                        'mb_soil_type_best',
                        'mb_soil_type_better',
                        'mb_soil_type_good',
                        'mb_blade_cutting_size',
                        'mb_blade_cutter_option',
                        'mb_bitcutting_size',
                        'mb_bit_thread',
                        'mb_transmitter_housing_type',
                        'mb_transmitter_diameter',
                        'mb_transmitter_front_connect',
                        'mb_transmitter_front_thread',
                        'mb_rig_model',
                        'mb_transmitter_feature',
                        'mb_eagle_claw_teeth',
                        'mb_ironfist_cutter',
                    ],
                    'Mud Motors' => [
                        'mb_rig_model',
                        'mb_manufacturer',
                        'mb_soil_type_best',
                        'mb_soil_type_better',
                        'mb_soil_type_good',
                    ],
                    'Quick-Disconnects' => [
                        'mb_quick_disconnect',
                        'mb_quick_disconnect_type',
                        'mb_slide_collar',
                        'mb_manufacturer',
                        'mb_rig_model',
                        'mb_adapter_gender',
                    ],
                    'Collars' => [
                        'mb_quick_disconnect',
                        'mb_quick_disconnect_type',
                        'mb_slide_collar',
                        'mb_manufacturer',
                        'mb_rig_model',
                    ],
                    'Swivels' => [
                        'mb_manufacturer',
                        'mb_reamer_back_thread',
                        'mb_reamer_rear_connection',
                        'mb_rig_model',
                        'mb_swivel_types',
                        'mb_swivel_connection',
                        'mb_swivel_thread',
                    ],
                    'Pullers' => [
                        'mb_duct_puller_size',
                    ],
                    'Drive Chucks & Sub-Savers' => [
                        'mb_driver_chuck_type',
                        'mb_manufacturer',
                        'mb_rig_model',
                    ],
                    'Adapters' => [
                        'mb_manufacturer',
                        'mb_reamer_front_thread',
                        'mb_reamer_back_thread',
                        'mb_reamer_shaft_size',
                        'mb_quick_disconnect',
                        'mb_transmitter_diameter',
                        'mb_transmitter_housing_type',
                        'mb_transmitter_front_connect',
                        'mb_rig_model',
                        'mb_adapter_option',
                        'mb_adapter_type',
                        'mb_thread_connection_type',
                        'mb_transmitter_rear_thread',
                        'mb_reamer_rear_connection',
                        'mb_adapter_gender',
                        'mb_swivel_capacities',
                        'mb_swivel_types',
                    ],
                    'Locators' => [
                        'mb_digitrak_falcon_model',
                        'mb_digitrak_aurora',
                        'mb_digitrak_falcon_locator',
                        'mb_transmitter_type',
                        'mb_digitrak_locator',
                    ],
                    'Auntie C\'s' => [
                        'mb_auntie_lube_type',
                        'mb_auntie_lube_size',
                        'mb_manufacturer',
                        'mb_rig_model',
                    ],
                    'Jaws' => [
                        'mb_breakout_jaw',
                        'mb_manufacturer',
                        'mb_rig_model',
                    ],

                ];

                foreach ($installedAttributeSets as $setObj) {
                    $position = 10;
                    $setName = $setObj->getAttributeSetName();
                    if (isset($attrSetRel[$setName])) {
                        foreach ($attrSetRel[$setName] as $attributeCode) {
                            echo $attributeCode . "\n";
                            $eavSetup->addAttributeToSet($entityType->getId(), $setObj->getId(), "MB {$setName}", $attributeCode, $position);
                            $position += 10;
                        }
                    }
                }
            } catch (\Exception $e) {
                die ($e);
            }
        }


        if (version_compare($context->getVersion(), '1.1.3', '<')) {
            $groupname = 'MelfredBorzall';

            $entityTypeCode = 'catalog_product';
            $entityType = $this->eavTypeFactory->create()->loadByCode($entityTypeCode);

            $attributes = [
                'mb_ground_condition' => [
                    'group' => $groupname,
                    'type' => 'int',
                    'label' => 'Top Ground Condition',
                    'input' => 'select',
                    'filterable' => true, //allow filter in layer navigation
                    'option' => [
                        'values' => [
                            0 => 'Dirt',
                            1 => 'Shale',
                            2 => 'Gravel',
                            3 => 'Sand',
                            4 => 'Sandstone',
                            5 => 'Cobbles',
                            6 => 'Clay',
                            7 => 'Hardpan',
                            8 => 'Caliche'
                        ]
                    ],
                ],

            ];

            try {
                foreach ($attributes as $attributeCode => $attributeInfo) {
                    $attributeExist = $eavSetup->getAttribute($entityType->getId(), $attributeCode);
                    if ($attributeExist) {
                        $eavSetup->removeAttribute(
                            $entityType->getId(), $attributeCode
                        );
                    }
                    if (!isset($attributeInfo['remove']) || $attributeInfo['remove'] != true) {
                        $eavSetup->addAttribute($entityType->getId(), $attributeCode,
                            array_merge(
                                [
                                    'backend' => '',
                                    'frontend' => '',
                                    'class' => '',
                                    'used_in_product_listing' => false,
                                    'system' => 0,
                                    'searchable' => false,
                                    'comparable' => false,
                                    'source' => '',
                                    'required' => false,
                                    'user_defined' => true,
                                    'default' => null,
                                    'visible_on_front' => true,
                                    'unique' => false,
                                    'apply_to' => 'simple,configurable,bundle',
                                    'visible' => true,
                                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,

                                ],
                                $attributeInfo
                            )
                        );
                    }
                }


            } catch (\Exception $e) {
                die ($e);
            }

        }

        if (version_compare($context->getVersion(), '1.1.4', '<')) {
            $entityTypeCode = 'catalog_product';
            $entityType = $this->eavTypeFactory->create()->loadByCode($entityTypeCode);
            $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
            $attributesList = [
                'mb_rig_model',
                'mb_soil_type_best',
                'mb_soil_type_better',
                'mb_soil_type_good'
            ];
            foreach ($attributesList as $attributeCode) {
                $eavSetup->updateAttribute(
                    $entityType->getId(),
                    $attributeCode,
                    'backend_model',
                    'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend'
                );
            }

            $installedAttributeSets = $entityType->getAttributeSetCollection();
            try {
                $attrSetRel = [
                    'Default' => [
                        'mb_soil_type_best',
                        'mb_soil_type_better',
                        'mb_soil_type_good',
                    ],
                ];

                foreach ($installedAttributeSets as $setObj) {
                    $position = 30;
                    $setName = $setObj->getAttributeSetName();
                    print_r($setName);
                    if (isset($attrSetRel[$setName])) {
                        foreach ($attrSetRel[$setName] as $attributeCode) {
                            echo $attributeCode . "\n";
                            $eavSetup->addAttributeToSet($entityType->getId(), $setObj->getId(), "MB {$setName}", $attributeCode, $position);
                            $position += 10;
                        }
                    }
                }
            } catch (\Exception $e) {
                die ($e);
            }

        }

        if (version_compare($context->getVersion(), '1.1.5', '<')) {

            //show in product detail
            $entityTypeCode = 'catalog_product';
            $entityType = $this->eavTypeFactory->create()->loadByCode($entityTypeCode);
            $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
            $attributesList = [
                'mb_rig_model',
                'mb_soil_type_best',
                'mb_soil_type_better',
                'mb_soil_type_good',
                'mb_bitcutting_size',
                'mb_transmitter_diameter',
                'mb_reamer_shaft_size',
                'mb_pulling_sling_cable',
                'mb_reamer_cutting_size',
            ];
            foreach ($attributesList as $attributeCode) {
                $eavSetup->updateAttribute(
                    $entityType->getId(),
                    $attributeCode,
                    'used_in_product_listing',
                    true
                );
            }

            $eavSetup->updateAttribute(
                $entityType->getId(),
                'mb_ground_condition', [
                    'used_in_product_listing' => true,
                    'user_defined' => false,
                    'backend_type' => 'varchar',
                    'frontend_input' => 'multiselect',
                    'backend_model' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend'
                ]
            );
        }

        if (version_compare($context->getVersion(), '1.1.6', '<')) {

            $entityTypeCode = 'catalog_product';
            $entityType = $this->eavTypeFactory->create()->loadByCode($entityTypeCode);
            $eavSetup->removeAttribute($entityType->getId(), 'mb_product_badge');
            $eavSetup->addAttribute($entityType->getId(), 'mb_product_badge',
                [
                    'group' => 'General',
                    'type' => 'varchar',
                    'label' => 'Product Badge',
                    'input' => 'multiselect',
                    'filterable' => false, //allow filter in layer navigation
                    'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
                    'frontend' => '',
                    'class' => '',
                    'used_in_product_listing' => true,
                    'system' => 0,
                    'searchable' => false,
                    'comparable' => false,
                    'source' => '',
                    'required' => false,
                    'user_defined' => true,
                    'sort_order' => 80,
                    'default' => null,
                    'visible_on_front' => true,
                    'unique' => false,
                    'apply_to' => 'simple,configurable,bundle,grouped',
                    'visible' => true,
                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                    'option' => [
                        'values' => [
                            1 => 'New',
                            2 => 'Redesigned',
                        ]
                    ],

                ]
            );
        }

        if (version_compare($context->getVersion(), '1.1.7', '<')) {

            $entityTypeCode = 'catalog_product';
            $entityType = $this->eavTypeFactory->create()->loadByCode($entityTypeCode);
            $eavSetup->updateAttribute(
                $entityType->getId(),
                'mb_ground_condition', [
                    'is_filterable' => 2,
                    'backend_model' => 'Forix\Product\Model\Config\Backend\GroundOptions'

                ]
            );
        }

        if (version_compare($context->getVersion(), '1.1.8', '<')) {
            $entityTypeCode = 'catalog_product';
            $entityType = $this->eavTypeFactory->create()->loadByCode($entityTypeCode);
            $attrs = [
                [
                    'label' => 'Redesigned date',
                    'code' => 'mb_redesigned_date'
                ],
                [
                    'label' => 'Download',
                    'code' => 'mb_content_download'
                ],
                [
                    'label' => 'Related Articles',
                    'code' => 'mb_related_articles'
                ]
            ];

            foreach ($attrs as $_attr) {
                $eavSetup->removeAttribute($entityType->getId(), $_attr['code']);
                $eavSetup->addAttribute($entityType->getId(), $_attr['code'],
                    [
                        'group' => 'Content',
                        'type' => 'text',
                        'backend' => '',
                        'frontend' => '',
                        'label' => $_attr['label'],
                        'input' => 'textarea',
                        'class' => '',
                        'source' => '',
                        'visible' => true,
                        'required' => false,
                        'user_defined' => false,
                        'default' => 0,
                        'searchable' => false,
                        'filterable' => true,
                        'comparable' => false,
                        'wysiwyg_enabled' => true,
                        'visible_on_front' => true,
                        'used_in_product_listing' => true,
                        'unique' => false,
                        'apply_to' => ''
                    ]
                );
            }
        }

        if (version_compare($context->getVersion(), '2.0.0', '<')) {
            $entityTypeCode = 'catalog_product';
            $entityType = $this->eavTypeFactory->create()->loadByCode($entityTypeCode);
            $eavSetup->updateAttribute($entityType->getId(), 'mb_reamer_shaft_size',
                [
                    'apply_to' => 'simple,virtual,configurable',
                ]
            );
        }

        if (version_compare($context->getVersion(), '2.0.1', '<')) {
            $entityTypeCode = 'catalog_product';
            $entityType = $this->eavTypeFactory->create()->loadByCode($entityTypeCode);
            $eavSetup->updateAttribute($entityType->getId(), 'mb_reamer_cutting_size',
                [
                    'apply_to' => 'simple,virtual,configurable',
                ]
            );
        }

        if (version_compare($context->getVersion(), '3.1.1', '<')) {
            $groupname = 'MelfredBorzall';

            $entityTypeCode = 'catalog_product';
            $entityType = $this->eavTypeFactory->create()->loadByCode($entityTypeCode);
            /// Update Attribute
            $eavSetup->updateAttribute($entityType->getId(), 'mb_auntie_lube_size', 'frontend_label', 'Auntie C\'s Thread Lube Size');
            $eavSetup->updateAttribute($entityType->getId(), 'mb_auntie_lube_type', 'frontend_label', 'Auntie C\'s Thread Lube Type');
            $eavSetup->updateAttribute($entityType->getId(), 'mb_blade_bolt_pattern', 'frontend_label', 'Blade Bolt Pattern');
            $eavSetup->updateAttribute($entityType->getId(), 'mb_blade_cutting_size', 'frontend_label', 'Pilot Hole Cutting Diameter'); /**/
            $eavSetup->updateAttribute($entityType->getId(), 'mb_break_connectorpins_type', 'frontend_label', 'Break-Away Connector Pin Type');
            $eavSetup->updateAttribute($entityType->getId(), 'mb_break_connector_type', 'frontend_label', 'Break-Away Connector Type');
            $eavSetup->updateAttribute($entityType->getId(), 'mb_breakout_jaw', 'frontend_label', 'Breakout Jaw Type');
            $eavSetup->updateAttribute($entityType->getId(), 'mb_digitrak_locator', 'frontend_label', 'Digitrak Locator Model');/**/
            $eavSetup->updateAttribute($entityType->getId(), 'mb_digitrak_falcon_locator', 'frontend_label', 'Digitrak Component');/**/
            $eavSetup->updateAttribute($entityType->getId(), 'mb_duct_puller_size', 'frontend_label', 'Duct Size'); /**/
            $eavSetup->updateAttribute($entityType->getId(), 'mb_ironfist_cutter', 'frontend_label', 'Iron Fist Cutter Block Type');

            $eavSetup->updateAttribute($entityType->getId(), 'mb_manufacturer', 'frontend_label', 'OEM');

            $eavSetup->updateAttribute($entityType->getId(), 'mb_pitbull_blades_styles', 'frontend_label', 'PitBull Blade Style');
            $eavSetup->updateAttribute($entityType->getId(), 'mb_pitbull_housing_size', 'frontend_label', 'PitBull Housing diameter');

            $eavSetup->updateAttribute($entityType->getId(), 'mb_pullback_grip', 'frontend_label', 'Pulling Grip Type');
            $eavSetup->updateAttribute($entityType->getId(), 'mb_quick_disconnect', 'frontend_label', 'Quick Disconnect Feature');
            $eavSetup->updateAttribute($entityType->getId(), 'mb_quick_disconnect_type', 'frontend_label', 'Quick Disconnect Type');
            $eavSetup->updateAttribute($entityType->getId(), 'mb_reamer_back_thread', 'frontend_label', 'Reamer Rear Thread');
            $eavSetup->updateAttribute($entityType->getId(), 'mb_reamer_rear_connection', 'frontend_label', 'Reamer Rear Connection Option');
            $eavSetup->updateAttribute($entityType->getId(), 'mb_rig_model', 'frontend_label', 'Rig Model');
            $eavSetup->updateAttribute($entityType->getId(), 'mb_slide_collar', 'frontend_label', 'Slide Collar Feature');
            $eavSetup->updateAttribute($entityType->getId(), 'mb_stabillizer_barrel', 'frontend_label', 'Stabillizer Barrel Feature');
            $eavSetup->updateAttribute($entityType->getId(), 'mb_swivel_capacities', 'frontend_label', 'Swivel Capacity');
            $eavSetup->updateAttribute($entityType->getId(), 'mb_swivel_types', 'frontend_label', 'Swivel Type');

            /// Add New Attribute
            $attributes = [
                'mb_pitbull_housing_feature' => [
                    'group' => $groupname,
                    'type' => 'int',
                    'label' => 'PitBull Housing Feature',
                    'input' => 'select',
                    'filterable' => 0, //allow filter in layer navigation
                    'option' => [
                        'values' => [
                            'PitBull',
                            'FastReam',
                        ]
                    ],
                ],
                'mb_pitbull_housing_rear_thread' => [
                    'group' => $groupname,
                    'type' => 'int',
                    'label' => 'PitBull Housing Rear Thread',
                    'input' => 'select',
                    'filterable' => 0, //allow filter in layer navigation
                    'option' => [
                        'values' => [
                            '1.66" FS200',
                            '2" IF',
                            '2-3/8" Reg',
                        ]
                    ],
                ],
                'mb_reamer_rear_flange' => [
                    'group' => $groupname,
                    'type' => 'int',
                    'label' => 'Reamer Rear Flange - size flange swivel to fit',
                    'input' => 'select',
                    'filterable' => 0, //allow filter in layer navigation
                    'option' => [
                        'values' => [
                            '10-ton',
                            '15-ton',
                            '20-ton',
                            '30-ton',
                            '40-ton',
                        ]
                    ],
                ],
                'mb_wiper_option' => [
                    'group' => $groupname,
                    'type' => 'int',
                    'label' => 'Wiper Option',
                    'input' => 'select',
                    'filterable' => 0, //allow filter in layer navigation
                    'option' => [
                        'values' => [
                            'SIngle 9"',
                            'Dual Split 9"',
                            'Single Split 9"',
                            'Dual Split 14"',
                        ]
                    ],
                ],
            ];
            foreach ($attributes as $attributeCode => $attributeInfo) {
                $eavSetup->addAttribute($entityType->getId(), $attributeCode,
                    array_merge(
                        [
                            'backend' => '',
                            'frontend' => '',
                            'class' => '',
                            'used_in_product_listing' => false,
                            'system' => 0,
                            'searchable' => true,
                            'comparable' => false,
                            'source' => '',
                            'required' => false,
                            'user_defined' => true,
                            'default' => null,
                            'visible_on_front' => true,
                            'unique' => false,
                            'apply_to' => 'simple,configurable,bundle',
                            'visible' => true,
                            'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,

                        ],
                        $attributeInfo
                    )
                );
            }

            /// Remove Attributes
            $eavSetup->removeAttribute($entityType->getId(), 'mb_bitcutting_size'); //
            $eavSetup->removeAttribute($entityType->getId(), 'mb_digitrak_falcon_model'); //
            $eavSetup->removeAttribute($entityType->getId(), 'mb_digitrak_aurora'); //
            $eavSetup->removeAttribute($entityType->getId(), 'mb_driver_chuck_type'); //
            $eavSetup->removeAttribute($entityType->getId(), 'mb_pitbull_bolt_pattern'); //
            $eavSetup->removeAttribute($entityType->getId(), 'mb_pitbull_housings'); //
        }

        if (version_compare($context->getVersion(), '3.2.1', '<')) {

            $entityTypeCode = 'catalog_product';
            $entityType = $this->eavTypeFactory->create()->loadByCode($entityTypeCode);
            $attributeId = $eavSetup->getAttributeId($entityType->getId(), 'color');
            $attributeSets = $eavSetup->getAllAttributeSetIds($entityType->getId());

            foreach ($attributeSets as $attributeSet) {
                $group = $eavSetup->getAttributeGroup($entityType->getId(), $attributeSet, 'General');
                $eavSetup->addAttributeToGroup($entityType->getId(), $attributeSet, $group['attribute_group_id'], $attributeId);
                $eavSetup->removeAttributeGroup($entityType->getId(), $attributeSet, "Blauer");
            }
        }

        if (version_compare($context->getVersion(), '3.3.1', '<')) {
            $entityTypeCode = 'catalog_product';
            $entityType = $this->eavTypeFactory->create()->loadByCode($entityTypeCode);
            $attrs = [
                [
                    'label' => 'Product Features',
                    'code' => 'mb_features'
                ],
                [
                    'label' => 'Small Description',
                    'code' => 'mb_small_description'
                ]
            ];

            foreach ($attrs as $_attr) {
                $eavSetup->removeAttribute($entityType->getId(), $_attr['code']);
                $eavSetup->addAttribute($entityType->getId(), $_attr['code'],
                    [
                        'group' => 'Content',
                        'type' => 'text',
                        'backend' => '',
                        'frontend' => '',
                        'label' => $_attr['label'],
                        'input' => 'textarea',
                        'class' => '',
                        'source' => '',
                        'visible' => true,
                        'required' => false,
                        'user_defined' => false,
                        'default' => 0,
                        'searchable' => false,
                        'filterable' => true,
                        'comparable' => false,
                        'wysiwyg_enabled' => true,
                        'visible_on_front' => true,
                        'used_in_product_listing' => true,
                        'unique' => false,
                        'apply_to' => ''
                    ]
                );
            }
        }
        if (version_compare($context->getVersion(), '3.4.1', '<')) {
            $entityTypeCode = 'catalog_product';
            $entityType = $this->eavTypeFactory->create()->loadByCode($entityTypeCode);
            $installedAttributeSets = $entityType->getAttributeSetCollection();
            foreach ($installedAttributeSets as $attributeSet) {
                $eavSetup->addAttributeToSet($entityType->getId(), $attributeSet->getId(), "Content", 'mb_features', 1000);
                $eavSetup->addAttributeToSet($entityType->getId(), $attributeSet->getId(), "Content", 'mb_small_description', 1000);
            }
        }

        if (version_compare($context->getVersion(), '3.4.2', '<')) {
            $entityTypeCode = 'catalog_product';
            $entityType = $this->eavTypeFactory->create()->loadByCode($entityTypeCode);
            $code = "mb_option_manual";
            $eavSetup->removeAttribute($entityType->getId(), $code);
            $eavSetup->addAttribute($entityType->getId(), $code,
                [
                    'group' => 'Content',
                    'type' => 'text',
                    'backend' => '',
                    'frontend' => '',
                    'label' => "show option (CDP)",
                    'input' => 'textarea',
                    'class' => '',
                    'source' => '',
                    'visible' => true,
                    'required' => false,
                    'user_defined' => false,
                    'default' => 0,
                    'searchable' => false,
                    'filterable' => true,
                    'comparable' => false,
                    'wysiwyg_enabled' => true,
                    'visible_on_front' => true,
                    'used_in_product_listing' => true,
                    'unique' => false,
                    'apply_to' => ''
                ]
            );
        }

        if (version_compare($context->getVersion(), '3.4.3', '<')) {
            $groupname = 'MelfredBorzall';
            $entityTypeCode = 'catalog_product';
            $entityType = $this->eavTypeFactory->create()->loadByCode($entityTypeCode);

            $attributes = [
                'mb_blade_feature' => [
                    'group' => $groupname,
                    'type' => 'text',
                    'label' => 'Blade Feature',
                    'input' => 'select',
                    'searchable' => true,
                    'required' => true,
                    'filterable' => true, //allow filter in layer navigation
                    'option' => [
                        'values' => [
                            0 => 'Standard',
                            1 => 'Pit Bull',
                            2 => 'Compatible'
                        ]
                    ],
                ],
                'mb_break_away_connector' => [
                    'group' => $groupname,
                    'type' => 'text',
                    'label' => 'Break-Away Connector Pin Strength',
                    'input' => 'select',
                    'searchable' => true,
                    'required' => true,
                    'filterable' => false, //allow filter in layer navigation
                    'option' => [
                        'values' => [
                            0 => '750 lbs',
                            1 => '1,000 lbs',
                            2 => '1,500 lbs',
                            3 => '2,000 lbs',
                            4 => '2,500 lbs',
                            5 => '3,000 lbs',
                            6 => '6,000 lbs',
                            7 => '7,000 lbs',
                            8 => '8,000 lbs',
                            9 => '9,000 lbs'
                        ]
                    ],
                ],

                'mb_break_range_required' => [
                    'group' => $groupname,
                    'type' => 'text',
                    'label' => 'Break Range Required',
                    'input' => 'select',
                    'searchable' => true,
                    'required' => true,
                    'filterable' => false, //allow filter in layer navigation
                    'option' => [
                        'values' => [
                            0 => 'Break from 750-12,500 lbs',
                            1 => 'Break from 3,000-45,000 lbs',
                            2 => 'Break from 750-2,500 lbs',
                            3 => 'Break from 750-12,500 lbs'
                        ]
                    ],
                ],

                'mb_break_away_type' => [
                    'group' => $groupname,
                    'type' => 'text',
                    'label' => 'Break-Away Type',
                    'input' => 'select',
                    'searchable' => true,
                    'required' => true,
                    'filterable' => true, //allow filter in layer navigation
                    'option' => [
                        'values' => [
                            0 => 'Light-Duty Connector',
                            1 => 'Heavy-Duty Connector',
                            2 => 'Light Duty Swivel',
                            3 => 'Heavy-Duty Swivel'
                        ]
                    ],
                ],

                'mb_digitrak_model' => [
                    'group' => $groupname,
                    'type' => 'text',
                    'label' => 'Digitrak Model',
                    'input' => 'select',
                    'searchable' => true,
                    'required' => true,
                    'filterable' => true, //allow filter in layer navigation
                    'option' => [
                        'values' => [
                            0 => 'Falcon F5',
                            1 => 'Falcon F2',
                            2 => 'Falcon F',
                            3 => 'F5',
                            4 => 'F2',
                            5 => 'Legacy Digitraks'
                        ]
                    ],
                ],

                'mb_digitrak_locator' => [
                    'group' => $groupname,
                    'type' => 'text',
                    'label' => 'Digitrak Locator',
                    'input' => 'select',
                    'searchable' => true,
                    'required' => true,
                    'filterable' => true, //allow filter in layer navigation
                    'option' => [
                        'values' => [
                            0 => 'System',
                            1 => 'Component',
                            2 => 'Transmitter'
                        ]
                    ],
                ],

                'mb_digitrak_system_feature' => [
                    'group' => $groupname,
                    'type' => 'text',
                    'label' => 'Digitrak System Feature',
                    'input' => 'select',
                    'searchable' => true,
                    'required' => true,
                    'filterable' => true, //allow filter in layer navigation
                    'option' => [
                        'values' => [
                            0 => 'System with 8.4" Aurora Display',
                            1 => 'System with 10.4" Aurora Display',
                            2 => 'System with 8.4" Aurora Display + iGPS',
                            3 => 'System with 10.4" Aurora Display + iGPS',
                            4 => 'System with FCD Display',
                            5 => 'System without Display'
                        ]
                    ],
                ],

                'mb_digitrak_transmit_feature' => [
                    'group' => $groupname,
                    'type' => 'text',
                    'label' => 'Digitrak Transmitter Feature',
                    'input' => 'select',
                    'searchable' => true,
                    'required' => true,
                    'filterable' => true, //allow filter in layer navigation
                    'option' => [
                        'values' => [
                            0 => 'Sub-K Rebar',
                            1 => '12/1.3kHz',
                            2 => '19/12kHz',
                            3 => '19/12kHz Extended Range',
                            4 => '8kHz',
                            5 => '12kHz',
                            6 => '18kHz',
                            7 => '19kHz',
                            8 => 'Wideband'
                        ]
                    ],
                ],

                'mb_digitrak_battery_rating' => [
                    'group' => $groupname,
                    'type' => 'text',
                    'label' => 'Digitrak Battery Rating',
                    'input' => 'select',
                    'searchable' => true,
                    'required' => true,
                    'filterable' => true, //allow filter in layer navigation
                    'option' => [
                        'values' => [
                            0 => '70 hours',
                            1 => '150 hours'
                        ]
                    ],
                ],

                'mb_digitrak_remote_display' => [
                    'group' => $groupname,
                    'type' => 'text',
                    'label' => 'DigiTrak Remote Display',
                    'input' => 'select',
                    'filterable' => true, //allow filter in layer navigation
                    'option' => [
                        'values' => [
                            0 => 'Aurora 8.4"',
                            1 => 'Aurora, 10.4"',
                            2 => 'FCD'
                        ]
                    ],
                ],

                'mb_duct_size' => [
                    'group' => $groupname,
                    'type' => 'text',
                    'label' => 'Duct Size',
                    'input' => 'select',
                    'searchable' => true,
                    'required' => true,
                    'filterable' => true, //allow filter in layer navigation
                    'option' => [
                        'values' => [
                            0 => '3/4"',
                            1 => '1"',
                            2 => '1-1/4"',
                            3 => '1-1/2"',
                            4 => '2"',
                            5 => '2-1/2"',
                            6 => '3"',
                            7 => '4"',
                            8 => '5"',
                            9 => '6"',
                            10 => '8"',
                            11 => '10"',
                            12 => '12"'
                        ]
                    ],
                ],

                'mb_eagle_claw_carbide_teeth' => [
                    'group' => $groupname,
                    'type' => 'text',
                    'label' => 'Eagle Claw Carbide Teeth',
                    'input' => 'select',
                    'searchable' => true,
                    'required' => true,
                    'filterable' => false, //allow filter in layer navigation
                    'option' => [
                        'values' => [
                            0 => 'Conical Aggressive',
                            1 => 'Dome',
                            2 => 'Large Conical Aggressive',
                            3 => 'Large Conical Aggressive Hardfaced'
                        ]
                    ],
                ],

                'mb_bit_type' => [
                    'group' => $groupname,
                    'type' => 'text',
                    'label' => 'Bit Type',
                    'input' => 'select',
                    'searchable' => true,
                    'required' => true,
                    'filterable' => false, //allow filter in layer navigation
                    'option' => [
                        'values' => [
                            0 => 'Borzall Blade',
                            1 => 'Eagle Claw',
                            2 => 'Iron Fist'
                        ]
                    ],
                ],

                'mb_pulling_grip_type' => [
                    'group' => $groupname,
                    'type' => 'text',
                    'label' => 'Pulling Grip Type',
                    'input' => 'select',
                    'searchable' => true,
                    'required' => true,
                    'filterable' => false, //allow filter in layer navigation
                    'option' => [
                        'values' => [
                            0 => 'Standard-Duty',
                            1 => 'Heavy-Duty',
                        ]
                    ],
                ],
                'mb_reamer_rear_thread' => [
                    'group' => $groupname,
                    'type' => 'text',
                    'label' => 'Reamer Rear Thread',
                    'input' => 'select',
                    'searchable' => true,
                    'required' => false,
                    'filterable' => false, //allow filter in layer navigation
                    'option' => [
                        'values' => [
                            0 => '2" IF',
                            1 => '2-3/8"REG',
                            2 => '2-7/8" IF',
                            3 => '3-1/2" IF',
                            4 => '4-1/2" IF'
                        ]
                    ],
                ],

                'mb_reamer_packing_size' => [
                    'group' => $groupname,
                    'type' => 'text',
                    'label' => 'Reamer Packing Size',
                    'input' => 'select',
                    'searchable' => true,
                    'required' => true,
                    'filterable' => false, //allow filter in layer navigation
                    'option' => [
                        'values' => [
                            0 => '4"',
                            1 => '4-1/2"',
                            2 => '6"',
                            3 => '6-5/8"',
                            4 => '8-5/8"',
                            5 => '10-3/4"',
                            6 => '12-3/4"',
                            7 => '14"',
                            8 => '16"',
                            9 => '18"',
                            10 => '20"',
                            11 => '24"',
                            12 => '28"',
                            13 => '32"',
                            14 => '36"',
                            15 => '40"',
                            16 => '44"',
                            17 => '48"'
                        ]
                    ],
                ],

                'mb_soil_type' => [
                    'group' => $groupname,
                    'type' => 'text',
                    'label' => 'Soil Type',
                    'input' => 'select',
                    'searchable' => true,
                    'required' => false,
                    'filterable' => false, //allow filter in layer navigation
                    'option' => [
                        'values' => [
                            0 => 'Dirt',
                            1 => 'Sand',
                            2 => 'Clay',
                            3 => 'Shale',
                            4 => 'Caliche',
                            5 => 'Hardpan',
                            6 => 'Sandstone',
                            7 => 'Gravel',
                            8 => 'Cobble'
                        ]
                    ],
                ],

                'mb_swivel_capacity' => [
                    'group' => $groupname,
                    'type' => 'text',
                    'label' => 'Swivel Capacity',
                    'input' => 'select',
                    'filterable' => true, //allow filter in layer navigation
                    'option' => [
                        'values' => [
                            0 => '2-ton (4,000 lbs)',
                            1 => '2-1/2 ton (5,000 lbs)',
                            2 => '4-1/4 ton (8,500 lbs)',
                            3 => '5 ton (10,000 lbs)',
                            4 => '7-1/2 ton (15,000 lbs)',
                            5 => '10 ton (20,000 lbs)',
                            6 => '15 ton (30,000 lbs)',
                            7 => '20 ton (40,000 lbs)',
                            8 => '30 ton (60,000 lbs)',
                            9 => '40 ton (80,000 lbs)',
                            10 => '45 ton (90,000 lbs)',
                            11 => '50 ton (100,000 lbs)',
                            12 => '60 ton (120,000 lbs)',
                            13 => '80 ton (160,000 lbs)',
                            14 => '110 ton (220,000 lbs)',
                            15 => '150 ton (300,000 lbs)',
                            16 => '165 ton (330,000 lbs)',
                            17 => '220 ton (440,000 lbs)',
                            18 => '250 ton (500,000 lbs)',
                            19 => '275 ton (550,000 lbs)',
                            20 => '500 ton (1,000,000 lbs)'
                        ]
                    ],
                ],

                'mb_thread_option' => [
                    'group' => $groupname,
                    'type' => 'text',
                    'label' => 'Thread Option',
                    'input' => 'select',
                    'searchable' => true,
                    'required' => true,
                    'filterable' => false, //allow filter in layer navigation
                    'option' => [
                        'values' => [
                            0 => 'FS250',
                            1 => 'FS400',
                            2 => 'FS600',
                            3 => 'FS602',
                            4 => 'FS650',
                            5 => 'FS700',
                            6 => 'FS750',
                            7 => 'FS800',
                            8 => 'FS802',
                            9 => 'FS900',
                            10 => 'FS1000',
                            11 => 'HE275',
                            12 => 'HE350',
                            13 => 'HE390',
                            14 => '2-1/8" LP',
                            15 => '1-5/8" LP',
                            16 => 'QF300',
                            17 => 'QF400',
                            18 => 'QF460',
                            19 => 'QF700',
                            20 => '1.41-6',
                            21 => '1.30-5',
                            22 => '1.94-4'
                        ]
                    ],
                ],

                'mb_safety_accessory' => [
                    'group' => $groupname,
                    'type' => 'text',
                    'label' => 'Safety Accessory',
                    'input' => 'select',
                    'searchable' => true,
                    'required' => true,
                    'filterable' => false, //allow filter in layer navigation
                    'option' => [
                        'values' => [
                            0 => 'Electrical Glove',
                            1 => 'Leather Protector Glove',
                            2 => 'Electrical Boot',
                            3 => 'Electrical Shoe',
                            4 => 'Glove Bag'
                        ]
                    ],
                ],
            ];

            try {
                foreach ($attributes as $attributeCode => $attributeInfo) {
                    $attributeExist = $eavSetup->getAttribute($entityType->getId(), $attributeCode);
                    if ($attributeExist) {
                        $eavSetup->removeAttribute(
                            $entityType->getId(), $attributeCode
                        );
                    }

                    if (!isset($attributeInfo['remove']) || $attributeInfo['remove'] != true) {
                        $eavSetup->addAttribute($entityType->getId(), $attributeCode,
                            array_merge(
                                [
                                    'backend' => '',
                                    'frontend' => '',
                                    'class' => '',
                                    'used_in_product_listing' => false,
                                    'system' => 0,
                                    'searchable' => true,
                                    'comparable' => false,
                                    'source' => '',
                                    'required' => false,
                                    'user_defined' => true,
                                    'default' => null,
                                    'visible_on_front' => true,
                                    'unique' => false,
                                    'apply_to' => '',
                                    'visible' => true,
                                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,

                                ],
                                $attributeInfo
                            )
                        );
                    }
                }


            } catch (\Exception $e) {
                die ($e);
            }

        }

        if (version_compare($context->getVersion(), '3.4.4', '<')) {
            $entityTypeCode = 'catalog_product';
            $entityType = $this->eavTypeFactory->create()->loadByCode($entityTypeCode);

            $installedAttributeSets = $entityType->getAttributeSetCollection();
            try {
                $attrSetRel = [
                    'Default' => [
                        'mb_blade_cutting_size',
                        'mb_transmitter_front_connect',
                        'mb_transmitter_housing_type',
                        'mb_transmitter_diameter',
                        'mb_slide_collar',
                        'mb_manufacturer'
                    ],
                    'Reamer' => [
                        'mb_swivel_capacity',
                        'mb_reamer_packing_size',
                        'mb_reamer_rear_thread'

                    ],
                    'FastBack' => [
                        'mb_transmitter_diameter',
                        'mb_transmitter_feature',
                        'mb_transmitter_front_connect',
                        'mb_transmitter_front_thread',
                        'mb_transmitter_rear_thread',
                        'mb_fastream_cutter_block',
                        'mb_fastream_cutter_size',
                        'mb_blade_cutting_size',
                        'mb_blade_feature',
                        'mb_bit_type',
                        'mb_thread_option'

                    ],
                    'Transmitter Housings' => [
                        'mb_manufacturer',
                        'mb_transmitter_diameter',
                        'mb_transmitter_front_thread',
                        'mb_transmitter_rear_thread',
                        'mb_transmitter_feature',
                        'mb_transmitter_front_connect'

                    ],
                    'Bits & Blades' => [
                        'mb_manufacturer',
                        'mb_transmitter_front_thread',
                        'mb_transmitter_front_connect',
                        'mb_transmitter_feature',
                        'mb_blade_feature',
                        'mb_blade_cutting_size',
                        'mb_bit_type',

                    ],
                    'Mud Motors' => [

                    ],
                    'Quick-Disconnects' => [
                        'mb_manufacturer',
                        'mb_slide_collar',
                        'mb_thread_option',
                        'mb_slide_collar',
                        'mb_thread_option',
                        'mb_quick_disconnect'
                    ],

                    'Swivels' => [
                        'mb_swivel_capacity',
                        'mb_reamer_rear_connection'
                    ],
                    'Pullers' => [
                        'mb_duct_size',
                        'mb_pulling_grip_type',
                        'mb_pulling_sling_cable',
                        'mb_break_connector_type'
                    ],
                    'Drive Chucks & Sub-Savers' => [
                        'mb_manufacturer',
                        'mb_thread_option'
                    ],
                    'Adapters' => [
                        'mb_manufacturer',
                        'mb_swivel_capacities',
                        'mb_thread_option'
                    ],
                    'PARTS & ACCESSORIES' => [
                        'mb_auntie_lube_size',
                        'mb_auntie_lube_size',
                        'mb_safety_accessory'
                    ],
                    'Locators' => [
                        'mb_digitrak_system_feature',
                        'mb_digitrak_transmit_feature',
                        'mb_digitrak_remote_display',
                        'mb_digitrak_falcon_locator',
                        'mb_digitrak_battery_rating',
                        'mb_digitrak_model',
                        'mb_manufacturer'
                    ],

                    'Jaws' => [
                        'mb_breakout_jaw',
                        'mb_manufacturer',
                        'mb_thread_option'
                    ],

                ];

                foreach ($installedAttributeSets as $setObj) {
                    $position = 10;
                    $setName = $setObj->getAttributeSetName();
                    print_r($setName);
                    if (isset($attrSetRel[$setName])) {
                        foreach ($attrSetRel[$setName] as $attributeCode) {
                            echo $attributeCode . "\n";
                            $eavSetup->addAttributeToSet($entityType->getId(), $setObj->getId(), "MB {$setName}", $attributeCode, $position);
                            $position += 10;
                        }
                    }
                }
            } catch (\Exception $e) {
                die ($e);
            }
        }


        if (version_compare($context->getVersion(), '3.5.1', '<')) {
            $connection = $setup->getConnection();
            $eavTable = $setup->getConnection()->getTableName('eav_attribute');
            $sql = "Update {$eavTable} set is_required = 0 where attribute_code like 'mb_%'";
            $connection->query($sql);
        }


        if (version_compare($context->getVersion(), '3.5.2', '<')) {

            $entityTypeCode = 'catalog_product';
            $entityType = $this->eavTypeFactory->create()->loadByCode($entityTypeCode);

            $attributes = [
                'mb_oem' => [
                    'group' => $groupname,
                    'type' => 'int',
                    'label' => 'OEM',
                    'input' => 'multiselect',
                    'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
                    'apply_to' => 'simple,configurable,bundle,grouped',
                    'filterable' => 0, //allow filter in layer navigation
                    'option' => [
                        'values' => [
                            'Vermeer',
                            'Ditch Witch',
                            'Astec/Toro',
                            'TT Tech'
                        ]
                    ],
                ],
            ];
            foreach ($attributes as $attributeCode => $attributeInfo) {
                $eavSetup->addAttribute($entityType->getId(), $attributeCode,
                    array_merge(
                        [
                            'backend' => '',
                            'frontend' => '',
                            'class' => '',
                            'used_in_product_listing' => false,
                            'system' => 0,
                            'searchable' => true,
                            'comparable' => false,
                            'source' => '',
                            'required' => false,
                            'user_defined' => true,
                            'default' => null,
                            'visible_on_front' => true,
                            'unique' => false,
                            'apply_to' => 'simple,configurable,bundle',
                            'visible' => true,
                            'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,

                        ],
                        $attributeInfo
                    )
                );
            }
        }

        if (version_compare($context->getVersion(), '3.5.3', '<')) {
            $entityTypeCode = 'catalog_product';
            $removeAttributes = [
                'mb_swivel_capacities',
                'mb_reamer_back_thread',
                'mb_pullback_grip',
                'mb_eagle_claw_teeth',
                'mb_duct_puller_size',
                'mb_digitrak_locator',
            ];
            $entityType = $this->eavTypeFactory->create()->loadByCode($entityTypeCode);
            foreach ($removeAttributes as $attributeCode) {
                $eavSetup->removeAttribute($entityType->getId(), $attributeCode);
            }
        }


        if (version_compare($context->getVersion(), '3.5.4', '<')) {
            $connection = $setup->getConnection();
            $eavTable = $setup->getConnection()->getTableName('eav_attribute');
            $sql = "Update {$eavTable} set is_required = 0 where entity_type_id = 4 and attribute_code like 'mb_%'";
            $connection->query($sql);
        }


        if (version_compare($context->getVersion(), '3.6.3', '<')) {
            $groupname = 'MelfredBorzall';
            $entityTypeCode = 'catalog_product';
            $entityType = $this->eavTypeFactory->create()->loadByCode($entityTypeCode);

            $attributes = [
                'mb_blade_feature' => [
                    'group' => $groupname,
                    'type' => 'int',
                    'label' => 'Blade Feature',
                    'input' => 'select',
                    'searchable' => true,
                    'required' => true,
                    'filterable' => true, //allow filter in layer navigation
                    'option' => [
                        'values' => [
                            0 => 'Standard',
                            1 => 'Pit Bull',
                            2 => 'Compatible'
                        ]
                    ],
                ],
                'mb_break_away_connector' => [
                    'group' => $groupname,
                    'type' => 'int',
                    'label' => 'Break-Away Connector Pin Strength',
                    'input' => 'select',
                    'searchable' => true,
                    'required' => true,
                    'filterable' => false, //allow filter in layer navigation
                    'option' => [
                        'values' => [
                            0 => '750 lbs',
                            1 => '1,000 lbs',
                            2 => '1,500 lbs',
                            3 => '2,000 lbs',
                            4 => '2,500 lbs',
                            5 => '3,000 lbs',
                            6 => '6,000 lbs',
                            7 => '7,000 lbs',
                            8 => '8,000 lbs',
                            9 => '9,000 lbs'
                        ]
                    ],
                ],

                'mb_break_range_required' => [
                    'group' => $groupname,
                    'type' => 'int',
                    'label' => 'Break Range Required',
                    'input' => 'select',
                    'searchable' => true,
                    'required' => true,
                    'filterable' => false, //allow filter in layer navigation
                    'option' => [
                        'values' => [
                            0 => 'Break from 750-12,500 lbs',
                            1 => 'Break from 3,000-45,000 lbs',
                            2 => 'Break from 750-2,500 lbs',
                            3 => 'Break from 750-12,500 lbs'
                        ]
                    ],
                ],

                'mb_break_away_type' => [
                    'group' => $groupname,
                    'type' => 'int',
                    'label' => 'Break-Away Type',
                    'input' => 'select',
                    'searchable' => true,
                    'required' => true,
                    'filterable' => true, //allow filter in layer navigation
                    'option' => [
                        'values' => [
                            0 => 'Light-Duty Connector',
                            1 => 'Heavy-Duty Connector',
                            2 => 'Light Duty Swivel',
                            3 => 'Heavy-Duty Swivel'
                        ]
                    ],
                ],

                'mb_digitrak_model' => [
                    'group' => $groupname,
                    'type' => 'int',
                    'label' => 'Digitrak Model',
                    'input' => 'select',
                    'searchable' => true,
                    'required' => true,
                    'filterable' => true, //allow filter in layer navigation
                    'option' => [
                        'values' => [
                            0 => 'Falcon F5',
                            1 => 'Falcon F2',
                            2 => 'Falcon F',
                            3 => 'F5',
                            4 => 'F2',
                            5 => 'Legacy Digitraks'
                        ]
                    ],
                ],

                'mb_digitrak_locator' => [
                    'group' => $groupname,
                    'type' => 'int',
                    'label' => 'Digitrak Locator',
                    'input' => 'select',
                    'searchable' => true,
                    'required' => true,
                    'filterable' => true, //allow filter in layer navigation
                    'option' => [
                        'values' => [
                            0 => 'System',
                            1 => 'Component',
                            2 => 'Transmitter'
                        ]
                    ],
                ],

                'mb_digitrak_system_feature' => [
                    'group' => $groupname,
                    'type' => 'int',
                    'label' => 'Digitrak System Feature',
                    'input' => 'select',
                    'searchable' => true,
                    'required' => true,
                    'filterable' => true, //allow filter in layer navigation
                    'option' => [
                        'values' => [
                            0 => 'System with 8.4" Aurora Display',
                            1 => 'System with 10.4" Aurora Display',
                            2 => 'System with 8.4" Aurora Display + iGPS',
                            3 => 'System with 10.4" Aurora Display + iGPS',
                            4 => 'System with FCD Display',
                            5 => 'System without Display'
                        ]
                    ],
                ],

                'mb_digitrak_transmit_feature' => [
                    'group' => $groupname,
                    'type' => 'int',
                    'label' => 'Digitrak Transmitter Feature',
                    'input' => 'select',
                    'searchable' => true,
                    'required' => true,
                    'filterable' => true, //allow filter in layer navigation
                    'option' => [
                        'values' => [
                            0 => 'Sub-K Rebar',
                            1 => '12/1.3kHz',
                            2 => '19/12kHz',
                            3 => '19/12kHz Extended Range',
                            4 => '8kHz',
                            5 => '12kHz',
                            6 => '18kHz',
                            7 => '19kHz',
                            8 => 'Wideband'
                        ]
                    ],
                ],

                'mb_digitrak_battery_rating' => [
                    'group' => $groupname,
                    'type' => 'int',
                    'label' => 'Digitrak Battery Rating',
                    'input' => 'select',
                    'searchable' => true,
                    'required' => true,
                    'filterable' => true, //allow filter in layer navigation
                    'option' => [
                        'values' => [
                            0 => '70 hours',
                            1 => '150 hours'
                        ]
                    ],
                ],

                'mb_digitrak_remote_display' => [
                    'group' => $groupname,
                    'type' => 'int',
                    'label' => 'DigiTrak Remote Display',
                    'input' => 'select',
                    'filterable' => true, //allow filter in layer navigation
                    'option' => [
                        'values' => [
                            0 => 'Aurora 8.4"',
                            1 => 'Aurora, 10.4"',
                            2 => 'FCD'
                        ]
                    ],
                ],

                'mb_duct_size' => [
                    'group' => $groupname,
                    'type' => 'int',
                    'label' => 'Duct Size',
                    'input' => 'select',
                    'searchable' => true,
                    'required' => true,
                    'filterable' => true, //allow filter in layer navigation
                    'option' => [
                        'values' => [
                            0 => '3/4"',
                            1 => '1"',
                            2 => '1-1/4"',
                            3 => '1-1/2"',
                            4 => '2"',
                            5 => '2-1/2"',
                            6 => '3"',
                            7 => '4"',
                            8 => '5"',
                            9 => '6"',
                            10 => '8"',
                            11 => '10"',
                            12 => '12"'
                        ]
                    ],
                ],

                'mb_eagle_claw_carbide_teeth' => [
                    'group' => $groupname,
                    'type' => 'int',
                    'label' => 'Eagle Claw Carbide Teeth',
                    'input' => 'select',
                    'searchable' => true,
                    'required' => true,
                    'filterable' => false, //allow filter in layer navigation
                    'option' => [
                        'values' => [
                            0 => 'Conical Aggressive',
                            1 => 'Dome',
                            2 => 'Large Conical Aggressive',
                            3 => 'Large Conical Aggressive Hardfaced'
                        ]
                    ],
                ],

                'mb_bit_type' => [
                    'group' => $groupname,
                    'type' => 'int',
                    'label' => 'Bit Type',
                    'input' => 'select',
                    'searchable' => true,
                    'required' => true,
                    'filterable' => false, //allow filter in layer navigation
                    'option' => [
                        'values' => [
                            0 => 'Borzall Blade',
                            1 => 'Eagle Claw',
                            2 => 'Iron Fist'
                        ]
                    ],
                ],

                'mb_pulling_grip_type' => [
                    'group' => $groupname,
                    'type' => 'int',
                    'label' => 'Pulling Grip Type',
                    'input' => 'select',
                    'searchable' => true,
                    'required' => true,
                    'filterable' => false, //allow filter in layer navigation
                    'option' => [
                        'values' => [
                            0 => 'Standard-Duty',
                            1 => 'Heavy-Duty',
                        ]
                    ],
                ],
                'mb_reamer_rear_thread' => [
                    'group' => $groupname,
                    'type' => 'int',
                    'label' => 'Reamer Rear Thread',
                    'input' => 'select',
                    'searchable' => true,
                    'required' => false,
                    'filterable' => false, //allow filter in layer navigation
                    'option' => [
                        'values' => [
                            0 => '2" IF',
                            1 => '2-3/8"REG',
                            2 => '2-7/8" IF',
                            3 => '3-1/2" IF',
                            4 => '4-1/2" IF'
                        ]
                    ],
                ],

                'mb_reamer_packing_size' => [
                    'group' => $groupname,
                    'type' => 'int',
                    'label' => 'Reamer Packing Size',
                    'input' => 'select',
                    'searchable' => true,
                    'required' => true,
                    'filterable' => false, //allow filter in layer navigation
                    'option' => [
                        'values' => [
                            0 => '4"',
                            1 => '4-1/2"',
                            2 => '6"',
                            3 => '6-5/8"',
                            4 => '8-5/8"',
                            5 => '10-3/4"',
                            6 => '12-3/4"',
                            7 => '14"',
                            8 => '16"',
                            9 => '18"',
                            10 => '20"',
                            11 => '24"',
                            12 => '28"',
                            13 => '32"',
                            14 => '36"',
                            15 => '40"',
                            16 => '44"',
                            17 => '48"'
                        ]
                    ],
                ],

                'mb_soil_type' => [
                    'group' => $groupname,
                    'type' => 'int',
                    'label' => 'Soil Type',
                    'input' => 'select',
                    'searchable' => true,
                    'required' => false,
                    'filterable' => false, //allow filter in layer navigation
                    'option' => [
                        'values' => [
                            0 => 'Dirt',
                            1 => 'Sand',
                            2 => 'Clay',
                            3 => 'Shale',
                            4 => 'Caliche',
                            5 => 'Hardpan',
                            6 => 'Sandstone',
                            7 => 'Gravel',
                            8 => 'Cobble'
                        ]
                    ],
                ],

                'mb_swivel_capacity' => [
                    'group' => $groupname,
                    'type' => 'int',
                    'label' => 'Swivel Capacity',
                    'input' => 'select',
                    'filterable' => true, //allow filter in layer navigation
                    'option' => [
                        'values' => [
                            0 => '2-ton (4,000 lbs)',
                            1 => '2-1/2 ton (5,000 lbs)',
                            2 => '4-1/4 ton (8,500 lbs)',
                            3 => '5 ton (10,000 lbs)',
                            4 => '7-1/2 ton (15,000 lbs)',
                            5 => '10 ton (20,000 lbs)',
                            6 => '15 ton (30,000 lbs)',
                            7 => '20 ton (40,000 lbs)',
                            8 => '30 ton (60,000 lbs)',
                            9 => '40 ton (80,000 lbs)',
                            10 => '45 ton (90,000 lbs)',
                            11 => '50 ton (100,000 lbs)',
                            12 => '60 ton (120,000 lbs)',
                            13 => '80 ton (160,000 lbs)',
                            14 => '110 ton (220,000 lbs)',
                            15 => '150 ton (300,000 lbs)',
                            16 => '165 ton (330,000 lbs)',
                            17 => '220 ton (440,000 lbs)',
                            18 => '250 ton (500,000 lbs)',
                            19 => '275 ton (550,000 lbs)',
                            20 => '500 ton (1,000,000 lbs)'
                        ]
                    ],
                ],

                'mb_thread_option' => [
                    'group' => $groupname,
                    'type' => 'int',
                    'label' => 'Thread Option',
                    'input' => 'select',
                    'searchable' => true,
                    'required' => true,
                    'filterable' => false, //allow filter in layer navigation
                    'option' => [
                        'values' => [
                            0 => 'FS250',
                            1 => 'FS400',
                            2 => 'FS600',
                            3 => 'FS602',
                            4 => 'FS650',
                            5 => 'FS700',
                            6 => 'FS750',
                            7 => 'FS800',
                            8 => 'FS802',
                            9 => 'FS900',
                            10 => 'FS1000',
                            11 => 'HE275',
                            12 => 'HE350',
                            13 => 'HE390',
                            14 => '2-1/8" LP',
                            15 => '1-5/8" LP',
                            16 => 'QF300',
                            17 => 'QF400',
                            18 => 'QF460',
                            19 => 'QF700',
                            20 => '1.41-6',
                            21 => '1.30-5',
                            22 => '1.94-4'
                        ]
                    ],
                ],

                'mb_safety_accessory' => [
                    'group' => $groupname,
                    'type' => 'int',
                    'label' => 'Safety Accessory',
                    'input' => 'select',
                    'searchable' => true,
                    'required' => true,
                    'filterable' => false, //allow filter in layer navigation
                    'option' => [
                        'values' => [
                            0 => 'Electrical Glove',
                            1 => 'Leather Protector Glove',
                            2 => 'Electrical Boot',
                            3 => 'Electrical Shoe',
                            4 => 'Glove Bag'
                        ]
                    ],
                ],
            ];

            try {
                foreach ($attributes as $attributeCode => $attributeInfo) {
                    $eavSetup->removeAttribute(
                        $entityType->getId(), $attributeCode
                    );

                    if (!isset($attributeInfo['remove']) || $attributeInfo['remove'] != true) {
                        $eavSetup->addAttribute($entityType->getId(), $attributeCode,
                            array_merge(
                                [
                                    'backend' => '',
                                    'frontend' => '',
                                    'class' => '',
                                    'used_in_product_listing' => false,
                                    'system' => 0,
                                    'searchable' => true,
                                    'comparable' => false,
                                    'source' => '',
                                    'required' => false,
                                    'user_defined' => true,
                                    'default' => null,
                                    'visible_on_front' => true,
                                    'unique' => false,
                                    'apply_to' => 'simple,virtual,configurable',
                                    'visible' => true,
                                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,

                                ],
                                $attributeInfo
                            )
                        );
                    }
                }


            } catch (\Exception $e) {
                die ($e);
            }

        }


        if (version_compare($context->getVersion(), '3.6.4', '<')) {
            $connection = $setup->getConnection();
            $eavTable = $setup->getConnection()->getTableName('eav_attribute');
            $sql = "Update {$eavTable} set is_required = 0 where entity_type_id = 4 and attribute_code like 'mb_%'";
            $connection->query($sql);
        }

        if (version_compare($context->getVersion(), '3.6.5', '<')) {
            $entityTypeCode = 'catalog_product';
            $entityType = $this->eavTypeFactory->create()->loadByCode($entityTypeCode);
            $attrs = [
                'mb_redesigned_date',
                'mb_content_download',
                'mb_related_articles',
                'mb_features',
                'mb_option_manual',
                'mb_small_description'
            ];

            foreach ($attrs as $_attr) {
                /// Update Attribute
                $eavSetup->updateAttribute($entityType->getId(), $_attr, 'default_value', '');
            }
        }


        if (version_compare($context->getVersion(), '3.7.2', '<')) {
            $entityTypeCode = 'catalog_product';
            $attributes = [
                'mb_blade_cutting_size',
                'mb_wiper_option',
                'mb_fastream_cutter_size',
                'mb_transmitter_diameter',
                'mb_swivel_thread',
                'mb_swivel_connection',
                'mb_swivel_capacity',
                'mb_safety_accessory',
                'mb_shoe_size',
                'mb_glove_size',
                'mb_transmitter_type',
                'mb_transmitter_feature',
                'mb_blade_bolt_pattern',
                'mb_thread_connection_type',
                'mb_swivel_types',
                'mb_stabillizer_barrel',
                'mb_reamer_packing_size',
                'mb_reamer_shaft_size',
                'mb_reamer_cutter_option',
                'mb_slide_collar',
                'mb_quick_disconnect_type',
                'mb_quick_disconnect',
                'mb_breakout_jaw',
                'mb_pulling_sling_cable',
                'mb_pulling_sling_legs',
                'mb_pulling_grip_type',
                'mb_duct_size',
                'mb_digitrak_locator',
                'mb_digitrak_transmit_feature',
                'mb_digitrak_remote_display',
                'mb_digitrak_falcon_locator',
                'mb_digitrak_battery_rating',
                'mb_digitrak_system_feature',
                'mb_break_away_connector',
                'mb_break_range_required',
                'mb_bit_type'
            ];
            $entityType = $this->eavTypeFactory->create()->loadByCode($entityTypeCode);
            foreach ($attributes as $attribute) {
                $eavSetup->updateAttribute($entityType->getId(), $attribute,
                    [
                        'apply_to' => 'simple,virtual,configurable',
                    ]
                );
            }
            $eavSetup->updateAttribute($entityType->getId(), 'mb_blade_cutting_size', 'frontend_label', 'Pilot Hole Cutting Diameter');

            //Pullers Attribute
            $entityTypeCode = 'catalog_product';
            $entityType = $this->eavTypeFactory->create()->loadByCode($entityTypeCode);
            $attributes = [
                'mb_breakaway_type' => [
                    'group' => $groupname,
                    'type' => 'int',
                    'label' => 'Breakaway Type',
                    'input' => 'select',
                    'filterable' => 0, //allow filter in layer navigation
                    'attribute_set' => 'Pullers',
                    'option' => [
                        'values' => [
                            'Light-Duty Connector', 'Light Duty Swivel', 'Heavy-Duty Swivel'
                        ]
                    ],
                ],
            ];
            $installedAttributeSets = $entityType->getAttributeSetCollection();
            foreach ($attributes as $attributeCode => $attributeInfo) {
                $eavSetup->addAttribute($entityType->getId(), $attributeCode,
                    array_merge(
                        [
                            'backend' => '',
                            'frontend' => '',
                            'class' => '',
                            'used_in_product_listing' => false,
                            'system' => 0,
                            'searchable' => true,
                            'comparable' => false,
                            'source' => '',
                            'required' => false,
                            'user_defined' => true,
                            'default' => null,
                            'visible_on_front' => true,
                            'unique' => false,
                            'apply_to' => 'simple,configurable,bundle',
                            'visible' => true,
                            'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,

                        ],
                        $attributeInfo
                    )
                );
                /**
                 * @var $attributeSet \Magento\Eav\Model\Entity\Attribute\Set
                 */
                foreach ($installedAttributeSets as $attributeSet) {
                    if (strtolower($attributeSet->getAttributeSetName()) == "pullers") {
                        $eavSetup->addAttributeToSet($entityType->getId(), $attributeSet->getId(), "MB Pullers", $attributeCode, 1000);
                    }
                }
            }
            //Pullers Attribute
        }


        if (version_compare($context->getVersion(), '3.8.3', '<')) {
            $entityTypeCode = 'catalog_product';
            $entityType = $this->eavTypeFactory->create()->loadByCode($entityTypeCode);
            // -------------------------- Create Attribute
            $attributes = [
                'mb_pilot_cutting_diameter' => [
                    'group' => $groupname,
                    'type' => 'int',
                    'label' => 'Pilot Hole Cutting Diameter',
                    'input' => 'select',
                    'filterable' => true, //allow filter in layer navigation
                    'apply_to' => 'simple,virtual,configurable',
                    'option' => [
                        'values' => [
                            0 => '2.5"',
                            1 => '3"',
                            2 => '3.5"',
                            3 => '4"',
                            4 => '4-1/2"',
                            5 => '5"',
                            6 => '5-1/2"',
                            7 => '7-1/2"',
                            8 => '9"',
                        ]
                    ],
                ],
                'mb_digitrak_component' => [
                    'group' => $groupname,
                    'type' => 'int',
                    'label' => 'Digitrak Component',
                    'input' => 'select',
                    'filterable' => true, //allow filter in layer navigation
                    'apply_to' => 'simple,virtual,configurable',
                    'option' => [
                        'values' => [
                            1 => 'System',
                            2 => 'Receiver',
                            3 => 'Remote Display',
                            4 => 'Charger',
                            5 => 'Transmitter',
                            6 => 'Battery',
                            7 => 'Transmitter Battery',
                            8 => 'GPS',
                            9 => 'Software',
                        ]
                    ],
                ],
                'mb_fit_transmitter_diameter' => [
                    'group' => $groupname,
                    'type' => 'int',
                    'label' => 'Fit Transmitter Housing Diamter',
                    'filterable' => 1, //allow filter in layer navigation
                    'input' => 'multiselect',
                    'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
                    'apply_to' => 'simple,configurable,bundle,grouped',
                    'option' => [
                        'values' => [
                            1 => '2"',
                            2 => '2-1/2"',
                            3 => '2-3/4"',
                            4 => '3"',
                            5 => '3-1/4"',
                            6 => '3-1/2"',
                            7 => '4-1/4"',
                            8 => '4-7/8"',
                            9 => '6-3/8"',
                        ]
                    ],
                ],
                'mb_reamer_rear_flange' => [
                    'group' => $groupname,
                    'type' => 'int',
                    'label' => 'Reamer Rear Flange - size flange swivel to fit',
                    'input' => 'multiselect',
                    'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
                    'apply_to' => 'simple,configurable,bundle,grouped',
                    'filterable' => 0, //allow filter in layer navigation
                    'option' => [
                        'values' => [
                            '10-ton',
                            '15-ton',
                            '20-ton',
                            '30-ton',
                            '40-ton',
                        ]
                    ],
                ],
                'mb_fit_reamer_rear_connection' => [
                    'group' => $groupname,
                    'type' => 'int',
                    'label' => 'Fit Reamer Rear Connection Option',
                    'input' => 'multiselect',
                    'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
                    'apply_to' => 'simple,configurable,bundle,grouped',
                    'filterable' => 0, //allow filter in layer navigation
                    'option' => [
                        'values' => [
                            1 => 'Bolt-on Flange Swivel',
                            2 => 'Thread-in Swivel',
                            3 => 'Pulling Eye',
                        ]
                    ],
                ],
                'mb_fit_stabillizer_barrel' => [
                    'group' => $groupname,
                    'type' => 'int',
                    'label' => 'Fit Stabillizer Barrel Feature',
                    'input' => 'select',
                    'filterable' => 0, //allow filter in layer navigation
                    'option' => [
                        'values' => [
                            1 => 'Plain',
                            2 => 'Value',
                            3 => 'Deluxe'
                        ]
                    ],
                ],
                'mb_transmitter_type' => [
                    'group' => $groupname,
                    'type' => 'int',
                    'label' => 'Transmitter Type',
                    'input' => 'multiselect',
                    'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
                    'apply_to' => 'simple,configurable,bundle,grouped',
                    'filterable' => 0, //allow filter in layer navigation
                    'option' => [
                        'values' => [
                            1 => 'Digitrak 15"',
                            2 => 'Digitrak 19"',
                            3 => 'Digitrak 15" cable',
                            4 => 'Digitrak 19" cable',
                            5 => 'Subsite 86BRP',
                            6 => 'Subsite 86BRV',
                        ]
                    ],
                ],
                'mb_fit_hole_cutting_diameter' => [
                    'group' => $groupname,
                    'type' => 'int',
                    'label' => 'Fit Pilot Hole Cutting Diameter',
                    'input' => 'multiselect',
                    'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
                    'apply_to' => 'simple,configurable,bundle,grouped',
                    'option' => [
                        'values' => [
                            0 => '2.5"',
                            1 => '3"',
                            2 => '3.5"',
                            3 => '4"',
                            4 => '4-1/2"',
                            5 => '5"',
                            6 => '5-1/2"',
                            7 => '7-1/2"',
                            8 => '9"',
                        ]
                    ],
                ],
                'mb_digitrak_model' => [
                    'remove' => true
                ],
                'mb_digitrak_locator_model' => [
                    'group' => $groupname,
                    'type' => 'int',
                    'label' => 'Digitrak Locator Model',
                    'input' => 'select',
                    'filterable' => true, //allow filter in layer navigation
                    'option' => [
                        'values' => [
                            0 => 'Falcon F5',
                            1 => 'Falcon F2',
                            2 => 'Falcon F',
                            3 => 'F5',
                            4 => 'F2',
                            5 => 'Legacy Digitraks'
                        ]
                    ],
                ],
                'mb_blade_cutting_size' => [
                    'remove' => true
                ],
                'mb_stabillizer_barrel' => [
                    'group' => $groupname,
                    'type' => 'int',
                    'label' => 'Stabillizer Barrel Options',
                    'input' => 'select',
                    'filterable' => 0, //allow filter in layer navigation
                    'option' => [
                        'values' => [
                            1 => 'Plain',
                            2 => 'Value',
                            3 => 'Deluxe',

                        ]
                    ],
                ]
            ];
            foreach ($attributes as $attributeCode => $attributeInfo) {
                $eavSetup->removeAttribute($entityType->getId(), $attributeCode);
                if (!isset($attributeInfo['remove']) || $attributeInfo['remove'] != true) {
                    $eavSetup->addAttribute($entityType->getId(), $attributeCode,
                        array_merge(
                            [
                                'backend' => '',
                                'frontend' => '',
                                'class' => '',
                                'used_in_product_listing' => false,
                                'system' => 0,
                                'searchable' => true,
                                'comparable' => false,
                                'source' => '',
                                'required' => false,
                                'user_defined' => true,
                                'default' => null,
                                'visible_on_front' => true,
                                'unique' => false,
                                'apply_to' => '',
                                'visible' => true,
                                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,

                            ],
                            $attributeInfo
                        )
                    );
                }
            }
            // -------------------------- Create Attribute
            $installedAttributeSets = $entityType->getAttributeSetCollection();
            $defaultSetId = $entityType->getDefaultAttributeSetId();

            $listAttributeSetToRemove = [
                'Reamer' => '1',
                'Reamers' => '1',
                'FastBack' => '1',
                'Transmitter Housings' => '1',
                'Bits & Blades' => '1',
                'Mud Motors' => '1',
                'Quick-Disconnects' => '1',
                'Collars' => '1',
                'Swivels' => '1',
                'Pullers' => '1',
                'Drive Chucks & Sub-Savers' => '1',
                'Adapters' => '1',
                'Parts & Accessories' => '1',
                'Locators' => '1',
                'Auntie C\'s' => '1',
                'Jaws' => '1',
            ];
            foreach ($installedAttributeSets as $setObj) {
                /**
                 * @var $setObj \Magento\Eav\Model\Entity\Attribute\Set
                 */
                $setName = $setObj->getAttributeSetName();
                if (isset($listAttributeSetToRemove[$setName])) {
                    $setObj->getResource()->delete($setObj);
                }
            }
            $listAttributeset = [
                'FastBack' => [
                    'mb_oem',
                    'mb_rig_model',
                    'mb_thread_option',
                    'mb_blade_bolt_pattern',
                    'mb_fastream_cutter_size',
                    'mb_fit_hole_cutting_diameter',
                    'mb_fastream_cutter_block',
                    'mb_bit_type',
                    'mb_fit_transmitter_diameter',
                    'mb_thread_connection_type',
                    'mb_transmitter_feature',
                    'mb_transmitter_front_connect',
                    'mb_transmitter_front_thread',
                    'mb_transmitter_rear_thread',
                    'mb_transmitter_type'
                ],
                'Bits & Blades' => [
                    'mb_oem',
                    'mb_rig_model',
                    'mb_bit_thread',
                    'mb_blade_bolt_pattern',
                    'mb_blade_feature',
                    'mb_blade_cutter_option',
                    'mb_bit_type',
                    'mb_pilot_cutting_diameter',
                    'mb_eagle_claw_carbide_teeth',
                    'mb_soil_type_best',
                    'mb_soil_type_better',
                    'mb_soil_type_good',
                    'mb_transmitter_front_connect',
                    'mb_transmitter_front_thread'
                ],
                'Adapters' => [
                    'mb_oem',
                    'mb_rig_model',
                    'mb_adapter_gender',
                    'mb_adapter_option',
                    'mb_adapter_type',
                    'mb_quick_disconnect_type',
                    'mb_swivel_capacity',
                    'mb_swivel_types',
                    'mb_thread_option',
                    'mb_fit_transmitter_diameter',
                    'mb_thread_connection_type'
                ],
                'Reamers' => [
                    'mb_oem',
                    'mb_rig_model',
                    'mb_reamer_cutter_option',
                    'mb_reamer_cutting_size',
                    'mb_reamer_packing_size',
                    'mb_reamer_rear_flange',
                    'mb_fit_reamer_rear_connection',
                    'mb_reamer_front_thread',
                    'mb_reamer_rear_thread',
                    'mb_reamer_shaft_size',
                    'mb_soil_type_best',
                    'mb_soil_type_better',
                    'mb_soil_type_good',
                    'mb_stabillizer_barrel',
                    'mb_fit_stabillizer_barrel',
                    'mb_swivel_capacity',
                    'mb_bit_type',
                    'mb_pilot_cutting_diameter',
                ],
                'Transmitter Housings' => [
                    'mb_oem',
                    'mb_rig_model',
                    'mb_blade_bolt_pattern',
                    'mb_transmitter_diameter',
                    'mb_transmitter_feature',
                    'mb_transmitter_front_connect',
                    'mb_transmitter_front_thread',
                    'mb_transmitter_rear_thread',
                    'mb_transmitter_housing_type',
                    'mb_transmitter_type'
                ],
                'Swivels' => [
                    'mb_oem',
                    'mb_rig_model',
                    'mb_reamer_rear_connection',
                    'mb_swivel_capacity',
                    'mb_swivel_connection',
                    'mb_swivel_thread',
                    'mb_swivel_types',
                ],
                'Quick-Disconnects' => [
                    'mb_oem',
                    'mb_rig_model',
                    'mb_quick_disconnect',
                    'mb_quick_disconnect_type',
                    'mb_slide_collar',
                ],
                'Pullers' => [
                    'mb_break_away_connector',
                    'mb_breakaway_type',
                    'mb_break_range_required',
                    'mb_duct_size',
                    'mb_pulling_grip_type',
                    'mb_pulling_sling_cable',
                    'mb_pulling_sling_legs',
                ],
                'Locators' => [
                    'mb_oem',
                    'mb_rig_model',
                    'mb_digitrak_locator_model',
                    'mb_digitrak_component',
                    'mb_transmitter_type',
                    'mb_digitrak_locator',
                    'mb_digitrak_system_feature',
                    'mb_digitrak_transmit_feature',
                    'mb_digitrak_remote_display',
                    'mb_digitrak_battery_rating'
                ],
                'Default' => [
                    'mb_oem',
                    'mb_rig_model',
                    'mb_breakout_jaw',
                    'mb_auntie_lube_size',
                    'mb_auntie_lube_type',
                    'mb_safety_accessory',
                    'mb_glove_size',
                    'mb_shoe_size',
                    'mb_wiper_option'
                ]
            ];
            $orders = 10;
            foreach ($listAttributeset as $newAttributeName => $attributeCodes) {
                $autosettingsTabName = "MB {$newAttributeName}";
                $data = [
                    'attribute_set_name' => $newAttributeName,
                    'entity_type_id' => $entityType->getId(),
                    'sort_order' => $orders,
                ];
                $orders += 10;
                $position = 0;
                $attributeSetId = $defaultSetId;
                if ('Default' !== $newAttributeName) {
                    $attributeSet = $this->attributeSetFactory->create();
                    $attributeSet->setData($data);
                    $this->attributeSetManagement->create($entityTypeCode, $attributeSet, $defaultSetId);
                    $attributeSetId = $attributeSet->getId();
                }
                $eavSetup->addAttributeGroup($entityType->getId(), $attributeSetId, $autosettingsTabName, $data['sort_order']);
                foreach ($attributeCodes as $attributeCode) {
                    echo $attributeCode . "\n";
                    $eavSetup->addAttributeToSet($entityType->getId(), $attributeSetId, $autosettingsTabName, $attributeCode, $position);
                    $position += 10;
                }
            }
        }
        if (version_compare($context->getVersion(), '3.8.4', '<')) {
            $entityTypeCode = 'catalog_product';
            $entityType = $this->eavTypeFactory->create()->loadByCode($entityTypeCode);
            // -------------------------- Create Attribute
            $attributes = [
                'mb_fit_transmitter_diameter' => [
                    'group' => $groupname,
                    'type' => 'varchar',
                    'label' => 'Fit Transmitter Housing Diamter',
                    'filterable' => 1, //allow filter in layer navigation
                    'input' => 'multiselect',
                    'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
                    'apply_to' => 'simple,configurable,bundle,grouped',
                    'option' => [
                        'values' => [
                            1 => '2"',
                            2 => '2-1/2"',
                            3 => '2-3/4"',
                            4 => '3"',
                            5 => '3-1/4"',
                            6 => '3-1/2"',
                            7 => '4-1/4"',
                            8 => '4-7/8"',
                            9 => '6-3/8"',
                        ]
                    ],
                ],
                'mb_fit_reamer_rear_connection' => [
                    'group' => $groupname,
                    'type' => 'varchar',
                    'label' => 'Fit Reamer Rear Connection Option',
                    'input' => 'multiselect',
                    'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
                    'apply_to' => 'simple,configurable,bundle,grouped',
                    'filterable' => 0, //allow filter in layer navigation
                    'option' => [
                        'values' => [
                            1 => 'Bolt-on Flange Swivel',
                            2 => 'Thread-in Swivel',
                            3 => 'Pulling Eye',
                        ]
                    ],
                ],
                'mb_reamer_rear_flange' => [
                    'group' => $groupname,
                    'type' => 'varchar',
                    'label' => 'Reamer Rear Flange - size flange swivel to fit',
                    'input' => 'multiselect',
                    'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
                    'apply_to' => 'simple,configurable,bundle,grouped',
                    'filterable' => 0, //allow filter in layer navigation
                    'option' => [
                        'values' => [
                            '10-ton',
                            '15-ton',
                            '20-ton',
                            '30-ton',
                            '40-ton',
                        ]
                    ],
                ],
                'mb_fit_stabillizer_barrel' => [
                    'group' => $groupname,
                    'type' => 'varchar',
                    'label' => 'Fit Stabillizer Barrel Feature',
                    'input' => 'multiselect',
                    'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
                    'apply_to' => 'simple,configurable,bundle,grouped',
                    'filterable' => 0, //allow filter in layer navigation
                    'option' => [
                        'values' => [
                            1 => 'Plain',
                            2 => 'Value',
                            3 => 'Deluxe'
                        ]
                    ],
                ],
                'mb_transmitter_type' => [
                    'group' => $groupname,
                    'type' => 'varchar',
                    'label' => 'Transmitter Type',
                    'input' => 'multiselect',
                    'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
                    'apply_to' => 'simple,configurable,bundle,grouped',
                    'filterable' => 0, //allow filter in layer navigation
                    'option' => [
                        'values' => [
                            1 => 'Digitrak 15"',
                            2 => 'Digitrak 19"',
                            3 => 'Digitrak 15" cable',
                            4 => 'Digitrak 19" cable',
                            5 => 'Subsite 86BRP',
                            6 => 'Subsite 86BRV',
                        ]
                    ],
                ],
                'mb_fit_hole_cutting_diameter' => [
                    'group' => $groupname,
                    'type' => 'varchar',
                    'label' => 'Fit Pilot Hole Cutting Diameter',
                    'input' => 'multiselect',
                    'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
                    'apply_to' => 'simple,configurable,bundle,grouped',
                    'option' => [
                        'values' => [
                            0 => '2.5"',
                            1 => '3"',
                            2 => '3.5"',
                            3 => '4"',
                            4 => '4-1/2"',
                            5 => '5"',
                            6 => '5-1/2"',
                            7 => '7-1/2"',
                            8 => '9"',
                        ]
                    ],
                ]
            ];
            foreach ($attributes as $attributeCode => $attributeInfo) {
                $eavSetup->removeAttribute($entityType->getId(), $attributeCode);
                if (!isset($attributeInfo['remove']) || $attributeInfo['remove'] != true) {
                    $eavSetup->addAttribute($entityType->getId(), $attributeCode,
                        array_merge(
                            [
                                'backend' => '',
                                'frontend' => '',
                                'class' => '',
                                'used_in_product_listing' => false,
                                'system' => 0,
                                'searchable' => true,
                                'comparable' => false,
                                'source' => '',
                                'required' => false,
                                'user_defined' => true,
                                'default' => null,
                                'visible_on_front' => true,
                                'unique' => false,
                                'apply_to' => '',
                                'visible' => true,
                                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,

                            ],
                            $attributeInfo
                        )
                    );
                }
            }
        }
        if (version_compare($context->getVersion(), '3.8.5', '<')) {
            $entityTypeCode = 'catalog_product';
            $entityType = $this->eavTypeFactory->create()->loadByCode($entityTypeCode);
            $installedAttributeSets = $entityType->getAttributeSetCollection();
            $listAttributeset = [
                'FastBack' => [
                    'mb_fit_hole_cutting_diameter',
                    'mb_fit_transmitter_diameter',
                    'mb_transmitter_type'
                ],
                'Adapters' => [
                    'mb_fit_transmitter_diameter'
                ],
                'Reamers' => [
                    'mb_reamer_rear_flange',
                    'mb_fit_reamer_rear_connection',
                    'mb_fit_stabillizer_barrel',
                ],
                'Transmitter Housings' => [
                    'mb_transmitter_type'
                ],
                'Locators' => [
                    'mb_transmitter_type'
                ],
            ];
            $orders = 10;
            foreach ($listAttributeset as $attributeName => $attributeCodes) {
                $position = 0;
                $autosettingsTabName = "MB {$attributeName}";
                foreach ($installedAttributeSets as $attributeSet) {
                    if (strtolower($attributeSet->getAttributeSetName()) == strtolower($attributeName)) {
                        foreach ($attributeCodes as $attributeCode) {
                            $eavSetup->addAttributeToSet($entityType->getId(), $attributeSet->getId(), $autosettingsTabName, $attributeCode, $position);
                            $position += 10;
                        }
                    }
                }
            }
        }

        if (version_compare($context->getVersion(), '3.8.6', '<')) {
            $entityTypeCode = 'catalog_product';
            $entityType = $this->eavTypeFactory->create()->loadByCode($entityTypeCode);
            // -------------------------- Create Attribute
            $attributes = [
                'mb_fit_final_hole_size' => [
                    'group' => $groupname,
                    'type' => 'varchar',
                    'label' => 'Fit Final Hole Size',
                    'filterable' => 1, //allow filter in layer navigation
                    'input' => 'multiselect',
                    'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
                    'apply_to' => 'simple,configurable,bundle,grouped',
                    'option' => [
                        'values' => [
                            1 => '4-1/2"',
                            2 => '6"',
                            3 => '8"',
                            4 => '10"',
                            5 => '12"'
                        ]
                    ],
                ],
            ];
            foreach ($attributes as $attributeCode => $attributeInfo) {
                $eavSetup->removeAttribute($entityType->getId(), $attributeCode);
                if (!isset($attributeInfo['remove']) || $attributeInfo['remove'] != true) {
                    $eavSetup->addAttribute($entityType->getId(), $attributeCode,
                        array_merge(
                            [
                                'backend' => '',
                                'frontend' => '',
                                'class' => '',
                                'used_in_product_listing' => false,
                                'system' => 0,
                                'searchable' => true,
                                'comparable' => false,
                                'source' => '',
                                'required' => false,
                                'user_defined' => true,
                                'default' => null,
                                'visible_on_front' => true,
                                'unique' => false,
                                'apply_to' => '',
                                'visible' => true,
                                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,

                            ],
                            $attributeInfo
                        )
                    );
                }
                $installedAttributeSets = $entityType->getAttributeSetCollection();
                foreach ($installedAttributeSets as $attributeSet) {
                    if (strtolower($attributeSet->getAttributeSetName()) == strtolower('Bits & Blades')) {
                        $eavSetup->addAttributeToSet($entityType->getId(), $attributeSet->getId(), 'MB Bits & Blades', $attributeCode, 100);
                        break;
                    }
                }
                foreach ($installedAttributeSets as $attributeSet) {
                    if (strtolower($attributeSet->getAttributeSetName()) == strtolower('FastBack')) {
                        $eavSetup->addAttributeToSet($entityType->getId(), $attributeSet->getId(), 'MB FastBack', $attributeCode, 100);
                        break;
                    }
                }
            }
        }
        if (version_compare($context->getVersion(), '3.8.7', '<')) {
            $entityTypeCode = 'catalog_product';
            $entityType = $this->eavTypeFactory->create()->loadByCode($entityTypeCode);
            // -------------------------- Create Attribute
            $attributes = [
                'mb_threads' => [
                    'group' => $groupname,
                    'type' => 'int',
                    'label' => 'Threads',
                    'input' => 'select',
                    'filterable' => false,
                    'apply_to' => 'simple,configurable,bundle,grouped,virtual'
                ],
                'mb_th_diameter_rear_thread' => [
                    'group' => $groupname,
                    'type' => 'int',
                    'label' => 'Transmitter Housing Diameter And Rear Thread',
                    'input' => 'select',
                    'filterable' => false,
                    'apply_to' => 'simple,configurable,bundle,grouped,virtual'
                ]
            ];
            foreach ($attributes as $attributeCode => $attributeInfo) {
                $eavSetup->removeAttribute($entityType->getId(), $attributeCode);
                if (!isset($attributeInfo['remove']) || $attributeInfo['remove'] != true) {
                    $eavSetup->addAttribute($entityType->getId(), $attributeCode,
                        array_merge(
                            [
                                'backend' => '',
                                'frontend' => '',
                                'class' => '',
                                'used_in_product_listing' => false,
                                'system' => 0,
                                'searchable' => true,
                                'comparable' => false,
                                'source' => '',
                                'required' => false,
                                'user_defined' => true,
                                'default' => null,
                                'visible_on_front' => true,
                                'unique' => false,
                                'apply_to' => '',
                                'visible' => true,
                                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,

                            ],
                            $attributeInfo
                        )
                    );
                }
                $installedAttributeSets = $entityType->getAttributeSetCollection();
                foreach ($installedAttributeSets as $attributeSet) {
                    if (strtolower($attributeSet->getAttributeSetName()) == strtolower('Adapters')) {
                        $eavSetup->addAttributeToSet($entityType->getId(), $attributeSet->getId(), 'MB Adapters', $attributeCode, 100);
                        break;
                    }
                }
                foreach ($installedAttributeSets as $attributeSet) {
                    if (strtolower($attributeSet->getAttributeSetName()) == strtolower('Adapters')) {
                        $eavSetup->addAttributeToSet($entityType->getId(), $attributeSet->getId(), 'MB Adapters', $attributeCode, 100);
                        break;
                    }
                }
            }
        }

        if (version_compare($context->getVersion(), '3.8.8', '<')) {
            $entityTypeCode = 'catalog_product';
            $entityType = $this->eavTypeFactory->create()->loadByCode($entityTypeCode);
            $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
            $attributesList = [
                'mb_redesigned_date',
                'mb_content_download',
                'mb_related_articles',
                'mb_features',
                'mb_small_description',
                'mb_option_manual'
            ];
            foreach ($attributesList as $attributeCode) {
                $eavSetup->updateAttribute(
                    $entityType->getId(),
                    $attributeCode,
                    'is_html_allowed_on_front',
                    true
                );
            }
        }
        return true;

    }


}
