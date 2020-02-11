<?php

namespace Makarovsoft\Notesoncustomers\Controller\Notes;

class Index extends \Makarovsoft\Notesoncustomers\Controller\Notes
{
    public function execute()
    {
        $resultPage = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_PAGE);
        return $resultPage;
    }
}