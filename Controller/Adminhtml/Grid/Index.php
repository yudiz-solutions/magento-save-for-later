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

namespace Yudiz\SaveForLater\Controller\Adminhtml\Grid;

class Index extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $_resultPageFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->_resultPageFactory = $resultPageFactory;
    }

    /**
     * Grid List page.
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->_resultPageFactory->create();
        $resultPage->setActiveMenu('Yudiz_SaveForLater::grid_list');
        $resultPage->getConfig()->getTitle()->prepend(__('Save For Later Products'));
        return $resultPage;
    }

    /**
     * Check Grid List Permission.
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Yudiz_SaveForLater::grid_list');
    }
}
