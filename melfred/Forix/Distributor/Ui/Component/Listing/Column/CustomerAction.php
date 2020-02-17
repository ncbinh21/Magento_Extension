<?php


namespace Forix\Distributor\Ui\Component\Listing\Column;

class CustomerAction extends \Magento\Customer\Ui\Component\Listing\Column\Actions
{
	/**
	 * Prepare Data Source
	 */
	public function prepareDataSource(array $dataSource)
	{
		if (isset($dataSource['data']['items'])) {
			$storeId = $this->context->getFilterParam('store_id');

			foreach ($dataSource['data']['items'] as &$item) {
				$item[$this->getData('name')]['edit'] = [
					'href' => $this->urlBuilder->getUrl(
						'forix_distributor/customer/edit',
						['id' => $item['entity_id'], 'store' => $storeId]
					),
					'label' => __('Edit'),
					'hidden' => false,
				];
			}
		}

		return $dataSource;
	}
}
