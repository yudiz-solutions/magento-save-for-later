<?php
namespace Yudiz\SaveForLater\Controller\Customer;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Index extends Action
{
    protected $resultPageFactory;

    public function __construct(
        Context $context,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    public function execute()
    {
        // Create a page result
        $resultPage = $this->resultPageFactory->create();

        // Set the page title
        //$resultPage->getConfig()->getTitle()->set(__('Clear Cart'));

        // Add your custom logic here
        // For example, you can display a success message or perform any other actions

        return $resultPage;
    }
}