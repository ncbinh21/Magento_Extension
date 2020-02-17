<?php

namespace Forix\Customer\Html;

use Magento\Framework\View\Element\Html\Links as htmlLinks;

class Links extends htmlLinks
{
	/**
	 * Render block HTML
	 *
	 * @return string
	 */
	protected function _toHtml()
	{
		if (false != $this->getTemplate()) {
			return parent::_toHtml();
		}

		$html = '';
		if ($this->getLinks()) {
			$groupFirst = $groupSecond = [];
			$i = 0;
			foreach ($this->getLinks() as $link) {
				if ($link->getLabel()!="") {
					if ($i<=7 ) {
						$groupFirst[] = $link;
					} else {
						$groupSecond[] =$link;
					}
					$i++;
				}
			}

			if (!empty($groupFirst)) {
				$html = '<ul' . ($this->hasCssClass() ? ' class="' . $this->escapeHtml(
							$this->getCssClass()
						) . '"' : '') . '>';
				foreach ($groupFirst as $link) {
					$html .= $this->renderLink($link);
				}

				$html .= '</ul>';
			}

			if (!empty($groupSecond)) {
				$html.= '<ul' . ($this->hasCssClass() ? ' class="' . $this->escapeHtml(
							$this->getCssClass()
						) . '"' : '') . '>';
				foreach ($groupSecond as $link) {
					$html .= $this->renderLink($link);
				}

				$html .= '</ul>';
			}
		}

		return $html;
	}
}