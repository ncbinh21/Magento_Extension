<?php
namespace Forix\FanPhoto\Ui\Component\Form;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Store\Model\StoreManagerInterface;

class CategoryOptions implements OptionSourceInterface
{
    /**
     * @var array
     */
    protected $options;


    public function __construct( StoreManagerInterface $storeManager)
    {
        $this->storeManager = $storeManager;
    }

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        if ($this->options !== null) {
            return $this->options;
        }

        $data = [
        	'Child' => 'Child',
            'Adult' => 'Adult'
        ];

        $options = [];
        foreach ($data as $key => $value) {
            $options[] = [
                'label' => $value,
                'value' => $key,
            ];
        }
        $this->options = $options;

        return $this->options;
    }
}
