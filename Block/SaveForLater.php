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

namespace Yudiz\SaveForLater\Block;

use Magento\Backend\Block\Template\Context;
use Magento\CatalogInventory\Model\Stock\StockItemRepository;
use Magento\Framework\Pricing\Helper\Data;
use Magento\Framework\View\Element\Template;
use Yudiz\SaveForLater\Model\ResourceModel\SaveForLater\CollectionFactory;

/**
 * Block class responsible for managing and displaying products saved for later by customers.
 */
class SaveForLater extends Template
{

    /**
     * @var CollectionFactory
     */
    public $collection;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productrepository;

    /**
     * @var StockItemRepository
     */
    protected $stockItemRepository;

    /**
     * @var Data
     */
    protected $priceHelper;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * SaveForLater constructor.
     *
     * @param Context $context
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productrepository
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Store\Model\StoreManagerInterface $storemanager
     * @param StockItemRepository $stockItemRepository
     * @param Data $priceHelper
     * @param CollectionFactory $collectionFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        \Magento\Catalog\Api\ProductRepositoryInterface $productrepository,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Store\Model\StoreManagerInterface $storemanager,
        StockItemRepository $stockItemRepository,
        Data $priceHelper,
        CollectionFactory $collectionFactory,
        array $data = []
    ) {
        $this->collection = $collectionFactory;
        $this->customerSession = $customerSession;
        $this->productrepository = $productrepository;
        $this->stockItemRepository = $stockItemRepository;
        $this->priceHelper = $priceHelper;
        $this->_storeManager = $storemanager;
        parent::__construct($context, $data);
    }

    /**
     * Prepare the layout and initialize the pager.
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        $this->pageConfig->getTitle()->set(__('Save For Later'));
        parent::_prepareLayout();
        $page_size = $this->getPagerCount();
        $page_data = $this->getSaveForLaterData();
        if ($this->getSaveForLaterData()) {
            $pager = $this->getLayout()->createBlock(
                \Magento\Theme\Block\Html\Pager::class,
                'custom.pager.name'
            )
                ->setAvailableLimit($page_size)
                ->setShowPerPage(true)
                ->setCollection($page_data);
            $this->setChild('pager', $pager);
            $this->getSaveForLaterData()->load();
        }
        return $this;
    }

    /**
     * Get Pager HTML.
     *
     * @return string
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    /**
     * Get Save For Later Data.
     *
     * @return \Yudiz\SaveForLater\Model\ResourceModel\SaveForLater\Collection
     */
    public function getSaveForLaterData()
    {
        // Get param values
        $page = ($this->getRequest()->getParam('p')) ? $this->getRequest()->getParam('p') : 1;
        $pageSize = ($this->getRequest()->getParam('limit')) ? $this->getRequest()->getParam('limit') : 6;

        // Get custom collection
        $customerId = $this->customerSession->getCustomer()->getId();
        $collections = $this->collection->create();
        $collections->addFieldToFilter('user_id', $customerId);
        $collections->setPageSize($pageSize);
        $collections->setCurPage($page);
        return $collections;
    }

    /**
     * Get Pager Count.
     *
     * @return array
     */
    public function getPagerCount()
    {
        $minimum_show = 6; // Set minimum records
        $page_array = [];
        $list_data = $this->collection->create();
        $list_count = ceil(count($list_data->getData()));
        $show_count = $minimum_show + 1;

        if (count($list_data->getData()) >= $show_count) {
            $list_count = $list_count / $minimum_show;
            $page_nu = $total = $minimum_show;
            $page_array[$minimum_show] = $minimum_show;

            for ($x = 0; $x <= $list_count; $x++) {
                $total = $total + $page_nu;
                $page_array[$total] = $total;
            }
        } else {
            $page_array[$minimum_show] = $minimum_show;
            $minimum_show = $minimum_show + $minimum_show;
            $page_array[$minimum_show] = $minimum_show;
        }
        return $page_array;
    }

    /**
     * Get Product Data Using ID.
     *
     * @param int $productId
     * @return array
     */
    public function getProductDataUsingId($productId)
    {
        $product_data = $this->productrepository->getById($productId);
        $stockItem = $this->stockItemRepository->get($productId);
        $store = $this->_storeManager->getStore();

        $productData = [
            'id' => $product_data->getId(),
            'name' => $product_data->getName(),
            'qty' => $stockItem->getQty(),
            'image' => $store->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) .
                'catalog/product' . $product_data->getImage(),
            'formatted_price' => $this->getFormattedPrice($product_data->getPrice()),
        ];
        return $productData;
    }

    /**
     * Get Formatted Price.
     *
     * @param float $price
     * @return string
     */
    public function getFormattedPrice($price)
    {
        return $this->priceHelper->currency($price, true, false);
    }

    /**
     * Get Product URL.
     *
     * @param int $productId
     * @return string
     */
    public function getProductUrl($productId)
    {
        $product = $this->productrepository->getById($productId);
        return $product->getProductUrl();
    }

    /**
     * Get Load More URL.
     *
     * @return string
     */
    public function getLoadMoreUrl()
    {
        return $this->getUrl('saveforlater/customer/loadmore');
    }

    /**
     * Get Loader Image URL.
     *
     * @return string
     */
    public function getLoaderImageUrl()
    {
        return $this->getViewFileUrl('images/loader-1.gif');
    }
}
