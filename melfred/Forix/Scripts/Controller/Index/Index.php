<?php
namespace Forix\Scripts\Controller\Index;

class Index extends \Magento\Framework\App\Action\Action
{
    protected $_creators;

    /**
     * Index constructor.
     * @param \Magento\Framework\App\Action\Context $context
     * @param array $creators
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        $creators = []
    ) {
        parent::__construct($context);
        $this->_creators = $creators;
    }

    /**
     * Execute view action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $process = $this->getRequest()->getParam('process');
        if($process) {
            $process = explode(',', $process);
            $process = array_map('strtolower',$process);
            foreach ($this->_creators as $key => $model) {
                if ($model instanceof \Forix\Scripts\Model\AbstractCreator) {
                    if (in_array(strtolower($model->getProcessCode()), $process)) {
                        echo $model->getProcessCode() . " ----------------------------------- \r\n <br/>";
                        $model->process($this->getRequest()->getParam('update'));
                        foreach ($model->getMessages() as $message) {
                            echo $message . "\r\n <br/>";
                        }
                    }
                }else{
                    echo "KEY: {$key} - ".get_class($model). " Doesn't instance of \Forix\Scripts\Model\AbstractCreator \r\n <br/>";
                }
            }
        }else {
            echo "Please Input Process Code \r\n";
        }
        exit();
    }
}
