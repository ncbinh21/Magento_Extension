<?php
/**
 * Created by PhpStorm.
 * User: nghiata
 * Date: 14/11/2017
 * Time: 01:16
 */

namespace Yosto\InstagramConnect\Model\System\Config;


use Magento\Framework\Option\ArrayInterface;
use Yosto\InstagramConnect\Model\ResourceModel\Template\CollectionFactory;
class TemplateList implements ArrayInterface
{
    protected $_templateCollectionFactory;
    function __construct(CollectionFactory $collectionFactory)
    {
        $this->_templateCollectionFactory = $collectionFactory;
    }

    public function toOptionArray()
    {
        /** @var \Yosto\InstagramConnect\Model\ResourceModel\Template\Collection $templatesCollection */
        $templatesCollection = $this->_templateCollectionFactory->create();
        $templates = $templatesCollection->addFieldToFilter('status', 1);

        $list = [];
        if ($templates->count() > 0) {
            foreach ($templates as $template) {
                $list[] = [
                    'label' => $template->getData('title'),
                    'value' => $template->getData('template_id')
                ];
            }
        } else {
            $list[] = ['label' => __('No Template Available'), 'value' => 0];
        }

        return $list;
    }

}