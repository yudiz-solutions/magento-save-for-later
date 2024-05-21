<?php
namespace Yudiz\SaveForLater\Controller\Index;

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
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        return $this->resultPageFactory->create();
    }

    public function updateBlockAction()
    {
        $layout = $this->_view->getLayout();
        $block = $layout->createBlock(\Yudiz\SaveForLater\Block\SaveForLater::class);
        $blockHtml = $block->toHtml();

        $this->getResponse()->setBody($blockHtml);
    }
}