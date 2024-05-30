<?php

/**
 * Yudiz
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to a newer
 * version in the future.
 *
 * @category    Yudiz
 * @package     Yudiz_SaveForLater
 * @copyright   Copyright (c) 2024 Yudiz (https://www.yudiz.com/)
 */

namespace Yudiz\SaveForLater\Ui\Component\Listing\Grid\Column;

use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Customer\Model\CustomerFactory;

/**
 * Class CustomerName
 *
 * Custom column to display customer name instead of customer ID in the grid
 */
class CustomerName extends Column
{
    /**
     * @var CustomerFactory
     */
    private $customerFactory;

    /**
     * CustomerName constructor.
     *
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param CustomerFactory $customerFactory
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        CustomerFactory $customerFactory,
        array $components = [],
        array $data = []
    ) {
        $this->customerFactory = $customerFactory;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                // Get customer name by ID
                if (isset($item['user_id'])) {
                    $customerId = $item['user_id'];
                    try {
                        $customer = $this->customerFactory->create()->load($customerId);
                        $item['user_id'] = $customer->getFirstname() . ' ' . $customer->getLastname();
                    } catch (\Exception $e) {
                        $item['user_id'] = 'N/A'; // Handle exception if customer is not found
                    }
                }
            }
        }
        return $dataSource;
    }
}
