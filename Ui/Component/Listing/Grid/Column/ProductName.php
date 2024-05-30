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
use Magento\Catalog\Api\ProductRepositoryInterface;

/**
 * Class ProductName
 *
 * Custom column to display product name instead of product ID in the grid
 */
class ProductName extends Column
{
    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * ProductName constructor.
     *
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param ProductRepositoryInterface $productRepository
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        ProductRepositoryInterface $productRepository,
        array $components = [],
        array $data = []
    ) {
        $this->productRepository = $productRepository;
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
                // Get product name by ID
                if (isset($item['product_id'])) {
                    $productId = $item['product_id'];
                    try {
                        $product = $this->productRepository->getById($productId);
                        $item['product_id'] = $product->getName();
                    } catch (\Exception $e) {
                        $item['product_id'] = 'N/A'; // Handle exception if product is not found
                    }
                }
            }
        }
        return $dataSource;
    }
}
