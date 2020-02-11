<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Paction
 */


namespace Amasty\Paction\Plugin\Ui\Model;

class AbstractReader
{
    /**
     * @var \Amasty\Paction\Helper\Data
     */
    protected $helper;
    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;

    public function __construct(
        \Amasty\Paction\Helper\Data $helper,
        \Magento\Framework\Module\Manager $moduleManager
    ) {
        $this->helper = $helper;
        $this->moduleManager = $moduleManager;
    }

    /**
    * Generate xml for creating one action
    */
    protected function generateElement($name)
    {
        $data = $this->helper->getActionDataByName($name);
        $placeholder = (array_key_exists('placeholder', $data)) ? $data['placeholder'] : '';

        $result = [
            'arguments' => [
                'data' => [
                    "name" => "data",
                    "xsi:type" => "array",
                    "item" => [
                        'config' => [
                            "name" => "config",
                            "xsi:type" => "array",
                            "item" => [
                                "component" => [
                                    "name" => "component",
                                    "xsi:type" => "string",
                                    "value" => "uiComponent"
                                ],
                                "amasty_actions" => [
                                    "name" => "component",
                                    "xsi:type" => "string",
                                    "value" => 'true'
                                ],
                                "confirm" => [
                                    "name" => "confirm",
                                    "xsi:type" => "array",
                                    "item" => [
                                        "title" => [
                                            "name" => "title",
                                            "xsi:type" => "string",
                                            "translate" => "true",
                                            "value" => $data['confirm_title']
                                        ],
                                        "message" => [
                                            "name" => "message",
                                            "xsi:type" => "string",
                                            "translate" => "true",
                                            "value" => $data['confirm_message']
                                        ]
                                    ]
                                ],
                                "type" => [
                                    "name" => "type",
                                    "xsi:type" => "string",
                                    "value" => 'amasty_' . $data['type']
                                ],
                                "label" => [
                                    "name" => "label",
                                    "xsi:type" => "string",
                                    "translate" => "true",
                                    "value" => $data['label']
                                ],
                                "url" => [
                                    "name" => "url",
                                    "xsi:type" => "url",
                                    "path" => $data['url']
                                ]

                            ]
                        ]
                    ]
                ],
                'actions' => [
                    "name" => "actions",
                    "xsi:type" => "array",
                    'item' => [
                        0 => [
                            "name" => "0",
                            "xsi:type" => "array",
                            "item" => [
                                "typefield" => [
                                    "name" => "type",
                                    "xsi:type" => "string",
                                    "value" => "textbox"
                                ],
                                "fieldLabel" => [
                                    "name" => "fieldLabel",
                                    "xsi:type" => "string",
                                    "value" => $data['fieldLabel']
                                ],
                                "placeholder" => [
                                    "name" => "placeholder",
                                    "xsi:type" => "string",
                                    "value" => $placeholder
                                ],
                                "label" => [
                                    "name" => "label",
                                    "xsi:type" => "string",
                                    "translate" => "true",
                                    "value" => ""
                                ],
                                "url" => [
                                    "name" => "url",
                                    "xsi:type" => "url",
                                    "path" => $data['url']
                                ],
                                "type" => [
                                    "name" => "type",
                                    "xsi:type" => "string",
                                    "value" => 'amasty_' . $data['type']
                                ],
                            ]
                        ]
                    ]
                ]
            ],
            'attributes' => [
                'class' => 'Magento\Ui\Component\Action',
                'name' => $name
            ],
            'children' => []

        ];

        if (array_key_exists('hide_input', $data)) {
            $result['arguments']['actions']['item'][0]['item']['hide_input'] = [
                "name" => "hide_input",
                "xsi:type" => "string",
                "value" => '1'
            ];
        }

        /*
         * his actions doesn't require input
        */
        if (strlen($name) <= 2 || $name == 'amdelete' || $name == 'removeimg') {
            unset($result['arguments']['actions']);
        }

        /*
         * this actions have select element on grid
         */
        if (in_array($name, ['unrelate', 'unupsell', 'uncrosssell'])) {
            $result['arguments']['actions']['item'][0]['item']['typefield']['value'] = 'select';
            $result['arguments']['actions']['item'][0]['item']['child'] = [
                "name" => "child",
                "xsi:type" => "array",
                'item' => [
                    0 => [
                        "name" => "0",
                        "xsi:type" => "array",
                        "item" => [
                            "label" => [
                                "name" => "label",
                                "xsi:type" => "string",
                                "value" => 'Remove relations between selected products only'
                            ],
                            "fieldvalue" => [
                                "name" => "fieldvalue",
                                "xsi:type" => "string",
                                "value" => '1'
                            ],
                        ]
                    ],
                    1 => [
                        "name" => "1",
                        "xsi:type" => "array",
                        "item" => [
                            "label" => [
                                "name" => "label",
                                "xsi:type" => "string",
                                "value" => 'Remove selected products from ALL relations in the catalog'
                            ],
                            "fieldvalue" => [
                                "name" => "fieldvalue",
                                "xsi:type" => "string",
                                "value" => '2'
                            ],
                        ]
                    ],
                    2 => [
                        "name" => "2",
                        "xsi:type" => "array",
                        "item" => [
                            "label" => [
                                "name" => "label",
                                "xsi:type" => "string",
                                "value" => 'Remove all relations from selected products'
                            ],
                            "fieldvalue" => [
                                "name" => "fieldvalue",
                                "xsi:type" => "string",
                                "value" => '3'
                            ],
                        ]
                    ]
                ]
            ];
        }

        return $result;
    }
}
