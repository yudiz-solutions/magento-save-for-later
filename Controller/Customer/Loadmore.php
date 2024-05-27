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

namespace Yudiz\SaveForLater\Controller\Customer;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Customer\Model\Session;
use Yudiz\SaveForLater\Model\ResourceModel\SaveForLater\CollectionFactory;

class Loadmore extends Action
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var JsonFactory
     */
    protected $jsonFactory;

    /**
     * @var Session
     */
    protected $customerSession;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * Constructor.
     *
     * @param Context           $context
     * @param PageFactory       $resultPageFactory
     * @param JsonFactory       $jsonFactory
     * @param Session           $customerSession
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        JsonFactory $jsonFactory,
        Session $customerSession,
        CollectionFactory $collectionFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->jsonFactory = $jsonFactory;
        $this->customerSession = $customerSession;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * Execute action
     *
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        $currentPage = (int)$this->getRequest()->getParam('page', 1);
        $pageSize = 6; // Or any other page size you want

        $resultPage = $this->resultPageFactory->create();
        $block = $resultPage->getLayout()
            ->createBlock(\Yudiz\SaveForLater\Block\SaveForLater::class)
            ->setTemplate('Yudiz_SaveForLater::saveforlater_loadmore.phtml')
            ->setData('current_page', $currentPage)
            ->setData('page_size', $pageSize);

        $saveForLaterCollection = $this->getSaveForLaterData($currentPage, $pageSize);
        $hasMoreData = $saveForLaterCollection->getSize() > $currentPage * $pageSize;

        $block->setData('save_for_later_collection', $saveForLaterCollection);

        return $this->jsonFactory->create()->setData([
            'success' => true,
            'html' => $block->toHtml(),
            'hasMoreData' => $hasMoreData
        ]);
    }

    /**
     * Retrieve Save For Later data
     *
     * @param int $currentPage
     * @param int $pageSize
     * @return \Yudiz\SaveForLater\Model\ResourceModel\SaveForLater\Collection
     */
    public function getSaveForLaterData($currentPage, $pageSize)
    {
        $customerId = $this->customerSession->getCustomerId();
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter('user_id', $customerId);

        // Calculate the offset based on the current page and page size
        $offset = ($currentPage - 1) * $pageSize;

        $collection->setPageSize($pageSize);
        $collection->setCurPage($currentPage);
        $collection->getSelect()->limit($pageSize, $offset);

        return $collection;
    }
}
