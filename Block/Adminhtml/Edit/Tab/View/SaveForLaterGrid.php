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

namespace Yudiz\SaveForLater\Block\Adminhtml\Edit\Tab\View;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Helper\Data;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Yudiz\SaveForLater\Model\ResourceModel\SaveForLater\CollectionFactory;
use Magento\Catalog\Api\ProductRepositoryInterface;

/**
 * Class SaveForLaterGrid
 * This class handles the grid for the "Save For Later" functionality in the admin panel.
 */
class SaveForLaterGrid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * SaveForLaterGrid constructor.
     *
     * @param Context $context
     * @param Data $backendHelper
     * @param CollectionFactory $collectionFactory
     * @param ProductRepositoryInterface $productRepository
     * @param Registry $coreRegistry
     * @param array $data
     */
    public function __construct(
        Context $context,
        Data $backendHelper,
        CollectionFactory $collectionFactory,
        ProductRepositoryInterface $productRepository,
        Registry $coreRegistry,
        array $data = []
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->_collectionFactory = $collectionFactory;
        $this->productRepository = $productRepository;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * Initialize grid parameters
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('view_custom_grid');
        $this->setDefaultSort('entity_id');
        $this->setFilterVisibility(false);
        $this->setUseAjax(true);
    }

    /**
     * Prepare collection
     *
     * @return \Magento\Backend\Block\Widget\Grid\Extended
     */
    protected function _prepareCollection()
    {
        $customerId = $this->getRequest()->getParam('id');
        if ($customerId) {
            $collection = $this->_collectionFactory->create();
            $collection->setCustomerId($customerId);
            $this->setCollection($collection);
        }
        return parent::_prepareCollection();
    }

    /**
     * Prepare columns
     *
     * @return \Magento\Backend\Block\Widget\Grid\Extended
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'product_name',
            [
                'header' => __('Product Name'),
                'index' => 'product_id',
                'type' => 'number',
                'width' => '100px',
                'frame_callback' => [$this, 'getProductName'],
            ]
        );
        $this->addColumn(
            'qty',
            [
                'header' => __('Product Qty'),
                'index' => 'qty',
            ]
        );
        return parent::_prepareColumns();
    }

    /**
     * Check headers visibility
     *
     * @return bool
     */
    public function getHeadersVisibility()
    {
        return $this->getCollection()->getSize() >= 0;
    }

    /**
     * Get row URL
     *
     * @param \Magento\Framework\DataObject $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('catalog/product/edit', ['id' => $row->getProductId()]);
    }

    /**
     * Get product name by ID
     *
     * @param string $value
     * @param \Magento\Framework\DataObject $row
     * @param \Magento\Backend\Block\Widget\Grid\Column $column
     * @param bool $isExport
     * @return string
     */
    public function getProductName($value, $row, $column, $isExport)
    {
        try {
            $product = $this->productRepository->getById($row->getData('product_id'));
            return $product->getName();
        } catch (NoSuchEntityException $e) {
            return __('Unknown Product');
        }
    }

    /**
     * Check if tab can be shown
     *
     * @return bool
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Check if tab is hidden
     *
     * @return bool
     */
    public function isHidden()
    {
        return false;
    }
}
