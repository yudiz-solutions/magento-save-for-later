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

namespace Yudiz\SaveForLater\Controller\Cart;

use Magento\CatalogInventory\Model\Stock\StockItemRepository;
use Magento\Catalog\Model\ProductFactory;
use Magento\Checkout\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Data\Form\FormKey;
use Magento\Framework\Json\Helper\Data;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\QuoteFactory;
use Psr\Log\LoggerInterface;
use Yudiz\SaveForLater\Model\ResourceModel\SaveForLater\CollectionFactory;
use Yudiz\SaveForLater\Model\SaveForLaterFactory;

class ManageCart extends Action
{
    /**
     * @var Session
     */
    protected $checkoutSession;

    /**
     * @var FormKey
     */
    protected $formKey;

    /**
     * @var StockItemRepository
     */
    protected $stockItemRepository;

    /**
     * @var JsonFactory
     */
    protected $jsonFactory;

    /**
     * @var Data
     */
    protected $jsonHelper;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Checkout\Model\Cart
     */
    protected $cart;

    /**
     * @var QuoteFactory
     */
    protected $quote;

    /**
     * @var CartRepositoryInterface
     */
    protected $cartRep;

    /**
     * @var ProductFactory
     */
    protected $productFactory;

    /**
     * @var Http
     */
    protected $request;

    /**
     * @var SaveForLaterFactory
     */
    protected $saveForLaterFactory;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * ManageCart constructor.
     *
     * @param Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param JsonFactory $jsonFactory
     * @param StockItemRepository $stockItemRepository
     * @param CollectionFactory $collectionFactory
     * @param SaveForLaterFactory $saveForLaterFactory
     * @param FormKey $formKey
     * @param ProductFactory $productFactory
     * @param Http $request
     * @param QuoteFactory $quote
     * @param CartRepositoryInterface $cartRap
     * @param Data $jsonHelper
     * @param \Magento\Checkout\Model\Cart $cart
     * @param LoggerInterface $logger
     */
    public function __construct(
        Context $context,
        \Magento\Customer\Model\Session $customerSession,
        JsonFactory $jsonFactory,
        StockItemRepository $stockItemRepository,
        CollectionFactory $collectionFactory,
        SaveForLaterFactory $saveForLaterFactory,
        FormKey $formKey,
        ProductFactory $productFactory,
        Http $request,
        QuoteFactory $quote,
        CartRepositoryInterface $cartRap,
        Data $jsonHelper,
        \Magento\Checkout\Model\Cart $cart,
        LoggerInterface $logger
    ) {
        $this->customerSession = $customerSession;
        $this->jsonFactory = $jsonFactory;
        $this->jsonHelper = $jsonHelper;
        $this->logger = $logger;
        $this->request = $request;
        $this->formKey = $formKey;
        $this->stockItemRepository = $stockItemRepository;
        $this->quote = $quote;
        $this->productFactory = $productFactory;
        $this->saveForLaterFactory = $saveForLaterFactory;
        $this->collectionFactory = $collectionFactory;
        $this->cartRep = $cartRap;
        $this->cart = $cart;
        parent::__construct($context);
    }

    /**
     * Ajax execute
     */
    public function execute()
    {
        // Get the request parameters
        $params = $this->request->getParams();
        // Access the action, product_id and quantity parameters
        $action = $params['action'] ?? null;
        $productId = $params['product_id'] ?? null;
        $requestedQty = $params['quantity'] ?? null;

        // Check if action is add or remove
        if ($action === 'add' && $productId && $requestedQty) {
            try {
                $product = $this->productFactory->create()->load($productId);
                $stockItem = $this->stockItemRepository->get($productId);

                // Check if the product is out of stock
                if (!$product->isSalable() || $stockItem->getIsInStock() == 0) {
                    $response = [
                        'errors' => true,
                        'message' => __('Product is out of stock.'),
                    ];
                } else {

                    // Get the existing quantity of the product in the cart
                    $existingQty = $this->getExistingQty($productId);
                    $totalRequestedQty = $requestedQty + $existingQty;
                    // Check if the total requested quantity is available
                    if ($stockItem->getQty() < $totalRequestedQty) {
                        $response = [
                            'errors' => true,
                            'message' => __('The requested qty is not available.'),
                        ];
                    } else {
                        $params = [
                            'form_key' => $this->formKey->getFormKey(),
                            'product' => $productId,
                            'qty' => $requestedQty,
                        ];
                        $this->cart->addProduct($product, $params);
                        $this->cart->save();
                        // Delete the product from the save for later list
                        $this->deleteProductId($productId);

                        $response = [
                            'success' => true,
                            'message' => __('Product added to cart successfully.'),
                        ];
                    }
                }
            } catch (\Exception $e) {
                $response = [
                    'errors' => true,
                    'message' => __('Something went wrong.'),
                ];
            }
        } elseif ($action === 'remove' && $productId) {
            if ($this->deleteProductId($productId)) {
                $response = [
                    'success' => true,
                    'message' => __('Product removed from save for later successfully.'),
                ];
            } else {
                $response = [
                    'errors' => true,
                    'message' => __('Unable to remove product from save for later.'),
                ];
            }
        } else {
            $response = [
                'errors' => true,
                'message' => __('Invalid action or missing parameters.'),
            ];
        }

        $resultJson = $this->jsonFactory->create();
        return $resultJson->setData($response);
    }

    /**
     * Get existing quantity of a product in the cart
     *
     * @param int $productId
     * @return int
     */
    private function getExistingQty(int $productId): int
    {
        $existingQty = 0;
        $quote = $this->cart->getQuote();
        $quoteItems = $quote->getAllItems();

        foreach ($quoteItems as $item) {
            if ($item->getProduct()->getId() == $productId) {
                $existingQty = $item->getQty();
                break;
            }
        }

        return $existingQty;
    }

    /**
     * Delete product ID from save for later
     *
     * @param int $productId
     * @return bool
     */
    public function deleteProductId(int $productId): bool
    {
        // Check if product exists in save for later
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter('product_id', $productId);
        $collection->addFieldToFilter('user_id', $this->customerSession->getCustomerId());

        if ($collection->getSize() > 0) {
            $saveForLater = $collection->getFirstItem();
            $saveForLater->delete();
            return true;
        }

        return false;
    }
}
