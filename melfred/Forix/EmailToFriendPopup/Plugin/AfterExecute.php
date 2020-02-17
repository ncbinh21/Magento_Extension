<?php

namespace Forix\EmailToFriendPopup\Plugin;

class AfterExecute
{
    /**
     * @var \Forix\EmailToFriendPopup\Helper\Data
     */
    protected $helper;

	public function __construct(
        \Forix\EmailToFriendPopup\Helper\Data $helper
	) {
		$this->helper = $helper;
	}

	public function afterExecute($source, $pageResult)
    {
		if($this->helper->allowPopup()) {
			$pageResult->getConfig()->setPageLayout('empty');
		}
		return $pageResult;
	}
}
