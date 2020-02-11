<?php

namespace Orange35\ImageConstructor\Controller\Adminhtml\Layer;

use Magento\Framework\Controller\ResultFactory;
use \Magento\Backend\App\Action\Context;
use \Magento\Backend\App\Action;
use \Orange35\ImageConstructor\Model\Uploader;

/**
 * Class Upload
 */
class Upload extends Action
{
    /**
     * @var string
     */
    const ACTION_RESOURCE = 'Orange35_ImageConstructor::layer';

    /**
     * uploader
     *
     * @var \Orange35\ImageConstructor\Model\Uploader
     */
    protected $uploader;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Orange35\ImageConstructor\Model\Uploader $uploader
     */
    public function __construct(
        Context $context,
        Uploader $uploader
    )
    {
        parent::__construct($context);
        $this->uploader = $uploader;
    }

    /**
     * Check admin permissions for this controller
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Orange35_ImageConstructor::layer');
    }

    /**
     * Upload file controller action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        try {
            $result = $this->uploader->saveFileToTmpDir($this->getFieldName());

            $result['cookie'] = [
                'name' => $this->_getSession()->getName(),
                'value' => $this->_getSession()->getSessionId(),
                'lifetime' => $this->_getSession()->getCookieLifetime(),
                'path' => $this->_getSession()->getCookiePath(),
                'domain' => $this->_getSession()->getCookieDomain(),
            ];
        } catch (\Exception $e) {
            $result = ['error' => $e->getMessage(), 'errorcode' => $e->getCode()];
        }
        return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData($result);
    }

    /**
     * @return string
     */
    protected function getFieldName()
    {
        return $this->_request->getParam('field');
    }
}