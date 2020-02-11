<?php

namespace Forix\FanPhoto\Controller\Register;

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Filesystem\DirectoryList;

class Action extends \Magento\Framework\App\Action\Action
{
	/**
	 * Constructor
	 *
	 * @param \Magento\Framework\App\Action\Context  $context
	 * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
	 */
	public function __construct(
		\Forix\FanPhoto\Model\EmailSend $emailSend,
		\Magento\Framework\App\Action\Context $context,
		\Magento\Framework\View\Result\PageFactory $resultPageFactory,
		\Forix\FanPhoto\Model\PhotoFactory $photoFactory,
		\Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory,
		\Magento\Framework\Filesystem $filesystem
	) {
		$this->resultPageFactory = $resultPageFactory;
		$this->photoModel = $photoFactory;
		$this->_fileUploaderFactory = $fileUploaderFactory;
		$this->_filesystem = $filesystem;
		$this->emailSend = $emailSend;
		parent::__construct($context);
	}

	/**
	 * Register action
	 *
	 * @return void
	 */
	public function execute()
	{
		$post = (array) $this->getRequest()->getPost();
		try {
			if ( ! empty( $post ) ) {
				$uploader = $this->_fileUploaderFactory->create( [ 'fileId' => 'image_url' ] );
				$uploader->setAllowedExtensions( [ 'jpg', 'jpeg', 'gif', 'png' ] );
				$uploader->setAllowRenameFiles( true );
				$uploader->setFilesDispersion( false );
				$path   = $this->_filesystem->getDirectoryRead( DirectoryList::MEDIA )->getAbsolutePath( 'fanphoto' );
				$result = $uploader->save( $path );
				if ( ! $result ) {
					throw new LocalizedException(
						__( 'File cannot be saved to path: $1', $path )
					);
				}
				$imagepath         = $result['file'];
				$data              = $post['data'];
				$data['image_url'] = $imagepath;
				$photo             = $this->photoModel->create();
				$photo->setData( $data );

				$result = $photo->save();
				if ( $result ) {
					$this->emailSend->execute();
					$this->messageManager->addSuccessMessage( 'Photo has been uploaded successfully !' );
				} else {
					$this->messageManager->addErrorMessage( 'Upload photo failed !' );
				}
			}
		}catch (\Exception $e){
			$this->messageManager->addErrorMessage( $e->getMessage() );
		}finally{
			$resultRedirect = $this->resultFactory->create( ResultFactory::TYPE_REDIRECT );
			$resultRedirect->setUrl( '/fanphoto' );

			return $resultRedirect;
		}

		if(empty( $post )){
			// Render the page
			$this->_view->loadLayout();
			$this->_view->renderLayout();
		}
	}
}