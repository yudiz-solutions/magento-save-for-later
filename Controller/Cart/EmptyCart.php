<?php

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

    protected $customerSession;

    /**
     * @var Magento\Checkout\Model\Cart
     */
    protected $saveForLaterFactory;
    protected $cart;
    protected $collectionFactory;

    /**
     * EmptyCart constructor.
     *
     * @param Context $context
     * @param Session $session
     * @param JsonFactory $jsonFactory
     * @param Data $jsonHelper
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
        $this->jsonHelper = $jsonHelper;
        $this->saveForLaterFactory = $saveForLaterFactory;
        $this->logger = $logger;
        $this->cart = $cart;
        parent::__construct($context);
    }

    /**
     * Ajax execute
     *
     */
    public function execute()
    {
        $cartObject = $this->cart->create();
        $items = $cartObject->getItems();
        if(!empty($items)){
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
    public function storeSaveForLater($product_id, $qty)
    {
        $saveForLater = $this->saveForLaterFactory->create();
        $saveForLater->setUserId($this->customerSession->getCustomerId());
        $saveForLater->setProductId($product_id);
        $saveForLater->setQty($qty);
        $saveForLater->save();
    }
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
