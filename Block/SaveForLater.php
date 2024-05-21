<?php

namespace Yudiz\SaveForLater\Block;

use Magento\Backend\Block\Template\Context;
use Magento\CatalogInventory\Model\Stock\StockItemRepository;
use Magento\Framework\Pricing\Helper\Data;
use Magento\Framework\View\Element\Template;
use Yudiz\SaveForLater\Model\ResourceModel\SaveForLater\CollectionFactory;

class SaveForLater extends Template
{
    public $collection;
    protected $customerSession;
    protected $productrepository;
    protected $stockItemRepository;
    protected $priceHelper;
    protected $_storeManager;

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

    protected function _prepareLayout()
    {
        $this->pageConfig->getTitle()->set(__('My Custom Pagination'));
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
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    public function getSaveForLaterData()
    {
        // get param values
        $page = ($this->getRequest()->getParam('p')) ? $this->getRequest()->getParam('p') : 1;
        $pageSize = ($this->getRequest()->getParam('limit')) ? $this->getRequest()->getParam('limit') : 5; // set minimum records
        // get custom collection
        $customerId = $this->customerSession->getCustomer()->getId();
        $collections = $this->collection->create();
        $collections->addFieldToFilter('user_id', $customerId);
        $collections->setPageSize($pageSize);
        $collections->setCurPage($page);
        return $collections;
    }

    public function getPagerCount()
    {
        // get collection
        $minimum_show = 5; // set minimum records
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
    public function getProductDataUsingId($productId)
    {
        $product_data = $this->productrepository->getById($productId);
        $stockItem = $this->stockItemRepository->get($productId);
        $store = $this->_storeManager->getStore();
        $productData = [
            'id' => $product_data->getId(),
            'name' => $product_data->getName(),
            'qty' => $stockItem->getQty(),
            'image' => $store->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'catalog/product' . $product_data->getImage(),
            'formatted_price' => $this->getFormattedPrice($product_data->getPrice()),
        ];
        return $productData;
    }
    public function getFormattedPrice($price)
    {
        return $this->priceHelper->currency($price, true, false);
    }
}
