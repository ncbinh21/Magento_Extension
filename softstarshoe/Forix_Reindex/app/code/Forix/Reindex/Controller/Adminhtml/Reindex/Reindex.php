<?php

namespace Forix\Reindex\Controller\Adminhtml\Reindex;

use Magento\Backend\App\Action;

/**
 * Class Index
 * @package Forix\Reindex\Controller\Adminhtml\Reindex
 */
class Reindex extends Action
{
    /**
     * Check the permission to run it
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Forix_Reindex::reindex');
    }

    /**
     *
     * @return void
     */
    public function execute()
    {
        $disabled = explode(',', ini_get('disable_functions'));
        if (in_array('exec', $disabled)) {
            throw new Exception("exec function is disabled.");
        }

        $indexerIds = $this->getRequest()->getParam('indexer_ids');
        if (!is_array($indexerIds)) {
            $this->messageManager->addError(__('Please select indexers.'));
        } else {
            try {
                /*$indexerId = implode(' ', $indexerIds);
                //$php = exec('which php');
                //exec('nohup ' . '/Applications/AMPPS/php/bin/php' . ' ' . BP . '/bin/magento indexer:reindex ' . $indexerIds .  ' > /dev/null 2>&1 & echo $! >> ' . BP . '/var/reindex/reindex.log');
                //exec('( ulimit -s hard; nohup ' . $php . ' ' . BP . '/bin/magento indexer:reindex ' . $indexerIds .  ' > /dev/null 2>&1 & )');
                $php = 'php -d memory_limit=3024M';
                exec($php . ' ' . BP . '/bin/magento indexer:reindex ' . $indexerId .  ' > /dev/null 2>&1 &');
                $this->messageManager->addSuccessMessage(__(count($indexerIds) . ' item(s) rebuilt successfully.'));*/
                  foreach ($indexerIds as $indexerId) {
                    /* @var \Magento\Framework\Indexer\IndexerInterface $model */
                    $model = $this->_objectManager->get('Magento\Framework\Indexer\IndexerRegistry')->get($indexerId);
                    $model->reindexAll();
                  }
                $this->messageManager->addSuccess(
                    __('%1 indexer(s) were reindexed.', count($indexerIds))
                );

            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException(
                    $e,
                    __("We couldn't reindex indexer(s)' because of an error.")
                );
            }
        }
        $this->_redirect('indexer/indexer/list');
    }
}