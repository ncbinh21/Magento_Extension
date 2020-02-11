<?php
/**
 * Copyright © 2015 Makarovsoft. All rights reserved.
 */

namespace Makarovsoft\Notesoncustomers\Controller\Adminhtml\Notes;

class Update extends \Makarovsoft\Notesoncustomers\Controller\Adminhtml\Notes
{

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Makarovsoft\Notesoncustomers\Model\NotesFactory
     */
    protected $notesFactory;

    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $backendAuthSession;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Makarovsoft\Notesoncustomers\Model\NotesFactory $notesFactory,
        \Magento\Backend\Model\Auth\Session $backendAuthSession
    )
    {
        $this->notesFactory = $notesFactory;
        $this->backendAuthSession = $backendAuthSession;
        parent::__construct($context, $coreRegistry, $resultForwardFactory, $resultPageFactory);
    }
    /**
     * Customer orders grid
     *
     * @return \Magento\Framework\View\Result\Layout
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        $data = $this->getRequest()->getParams();

        /*
         * Process Edited
         */
        if (isset($data['mascnotes_note']) && count($data['mascnotes_note']) > 0) {
            foreach ($data['mascnotes_note'] as $noteId => $existing) {
                $note = $this->notesFactory->create()->load($noteId);
                if (isset($existing['delete'])) {
                    $note->delete();
                } else {
                    $note->setNote($existing['note'])
                        ->setCustomerId($id)
                        ->setVisible($existing['status'])
                        ->save();
                }
            }
        }

        /*
         * Process New
         */
        if ($data['mascnotes_new'][0]['note'] != '') {
            $this->notesFactory->create()
                ->setNote($data['mascnotes_new'][0]['note'])
                ->setCustomerId($id)
                ->setUserId($this->backendAuthSession->getUser()->getId())
                ->setVisible($data['mascnotes_new'][0]['status'])
                ->save();
        }


        $this->_view->loadLayout(false);
        $this->_view->renderLayout();
    }
}
