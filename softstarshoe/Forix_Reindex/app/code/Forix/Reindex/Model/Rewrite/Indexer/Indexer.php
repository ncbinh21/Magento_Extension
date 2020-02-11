<?php
namespace Forix\Reindex\Model\Rewrite\Indexer;

use Magento\Framework\Indexer\StateInterface;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Indexer extends \Magento\Indexer\Model\Indexer
{
    /**
     * Regenerate full index
     *
     * @return void
     * @throws \Exception
     */
    public function reindexAll()
    {
        if ($this->getState()->getStatus() == StateInterface::STATUS_WORKING) {
            $this->getState()->setStatus(StateInterface::STATUS_INVALID);
        }
        if ($this->getState()->getStatus() != StateInterface::STATUS_WORKING) {
            $state = $this->getState();
            $state->setStatus(StateInterface::STATUS_WORKING);
            $state->save();
            if ($this->getView()->isEnabled()) {
                $this->getView()->suspend();
            }
            try {
                $this->getActionInstance()->executeFull();
                $state->setStatus(StateInterface::STATUS_VALID);
                $state->save();
                $this->getView()->resume();
            } catch (\Exception $exception) {
                $state->setStatus(StateInterface::STATUS_INVALID);
                $state->save();
                $this->getView()->resume();
                throw $exception;
            } catch (\Error $error) {
                $state->setStatus(StateInterface::STATUS_INVALID);
                $state->save();
                $this->getView()->resume();
                throw $error;
            }
        }
    }

}
