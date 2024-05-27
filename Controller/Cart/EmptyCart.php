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

use Magento\Checkout\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Json\Helper\Data;
use Psr\Log\LoggerInterface;
use Yudiz\SaveForLater\Model\ResourceModel\SaveForLater\CollectionFactory;
use Yudiz\SaveForLater\Model\SaveForLaterFactory;

class EmptyCart extends Action
{
    /**
     * @var Session
     */
    protected $checkoutSession;

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
     * @var \Magento\Checkout\Model\CartFactory
     */
    protected $cart;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var SaveForLaterFactory
     */
    protected $saveForLaterFactory;

    /**
     * EmptyCart constructor.
     *
     * @param Context $context
     * @param CollectionFactory $collectionFactory
     * @param \Magento\Customer\Model\Session $customerSession
     * @param JsonFactory $jsonFactory
     * @param SaveForLaterFactory $saveForLaterFactory
     * @param Data $jsonHelper
     * @param \Magento\Checkout\Model\CartFactory $cart
     * @param LoggerInterface $logger
     */
    public function __construct(
        Context $context,
        CollectionFactory $collectionFactory,
        \Magento\Customer\Model\Session $customerSession,
        JsonFactory $jsonFactory,
        SaveForLaterFactory $saveForLaterFactory,
        Data $jsonHelper,
        \Magento\Checkout\Model\CartFactory $cart,
        LoggerInterface $logger
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->customerSession = $customerSession;
        $this->jsonFactory = $jsonFactory;
        $this->saveForLaterFactory = $saveForLaterFactory;
        $this->jsonHelper = $jsonHelper;
        $this->cart = $cart;
        $this->logger = $logger;
        parent::__construct($context);
    }

    /**
     * Ajax execute
     */
    public function execute()
    {
        $cartObject = $this->cart->create();
        $items = $cartObject->getItems();
        if (!empty($items)) {
            foreach ($items as $item) {
                if ($this->getProductExists($item->getProductId(), $item->getQty()) == true) {
                    continue;
                } else {
                    $this->storeSaveForLater($item->getProductId(), $item->getQty());
                }
            }
        }
        if ($this->getRequest()->isAjax()) {
            try {
                $cartTruncate = $cartObject->truncate();
                $cartTruncate->saveQuote();
                $response['success'] = true;
                $response['message'] = __('Product added to save for later successfully.');
            } catch (\Exception $e) {
                $response = [
                    'errors' => true,
                    'message' => __('Something went wrong.'),
                ];
                $this->logger->critical($e);
            }
        } else {
            $response = [
                'errors' => true,
                'message' => __('Need to access via Ajax.'),
            ];
        }

        $resultJson = $this->jsonFactory->create();
        return $resultJson->setData($response);
    }

    /**
     * Store product in Save For Later
     *
     * @param int $product_id
     * @param int $qty
     */
    public function storeSaveForLater($product_id, $qty)
    {
        $saveForLater = $this->saveForLaterFactory->create();
        $saveForLater->setUserId($this->customerSession->getCustomerId());
        $saveForLater->setProductId($product_id);
        $saveForLater->setQty($qty);
        $saveForLater->save();
    }

    /**
     * Check if product exists in Save For Later
     *
     * @param int $product_id
     * @param int $qty
     * @return bool
     */
    public function getProductExists($product_id, $qty)
    {
        //check product exists in save for later
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter('product_id', $product_id);
        $collection->addFieldToFilter('user_id', $this->customerSession->getCustomerId());
        if ($collection->getSize() > 0) {
            $saveForLater = $collection->getFirstItem();
            $saveForLater->setQty($saveForLater->getQty() + $qty);
            $saveForLater->save();
            return true;
        }
    }
}
