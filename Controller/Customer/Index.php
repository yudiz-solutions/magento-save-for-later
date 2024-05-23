<?php
namespace Yudiz\SaveForLater\Controller\Customer;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Message\ManagerInterface;

class Index extends Action
{
    protected $resultPageFactory;
    protected $customerSession;
    protected $resultRedirectFactory;
    protected $messageManager;

    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        CustomerSession $customerSession,
        RedirectFactory $resultRedirectFactory,
        ManagerInterface $messageManager,
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->customerSession = $customerSession;
        $this->resultRedirectFactory = $resultRedirectFactory;
        $this->messageManager = $messageManager;
    }

    public function execute()
    {
        // Check if the customer is logged in
        if (!$this->customerSession->isLoggedIn()) {
            // Redirect to the sign-in page if not logged in
            $this->messageManager->addErrorMessage(__("You need to Sign-in to save items for later."));
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('customer/account/login');
            return $resultRedirect;
        }

        // Create a page result if the customer is logged in
        $resultPage = $this->resultPageFactory->create();
        return $resultPage;
    }
}
