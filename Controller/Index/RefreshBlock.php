<?php
namespace Yudiz\SaveForLater\Controller\Index;

use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Yudiz\SaveForLater\Block\SaveForLater;

class RefreshBlock extends \Magento\Framework\App\Action\Action
{
    protected $jsonFactory;
    protected $saveForLaterBlock;

    public function __construct(
        Context $context,
        JsonFactory $jsonFactory,
        SaveForLater $saveForLaterBlock
    ) {
        $this->jsonFactory = $jsonFactory;
        $this->saveForLaterBlock = $saveForLaterBlock;
        parent::__construct($context);
    }

    public function execute()
    {
        $resultJson = $this->jsonFactory->create();
        try {
            // Retrieve the save for later data
            $saveForLaterData = $this->saveForLaterBlock->getSaveForLaterData();
            // Generate HTML using a template
            $html = $this->saveForLaterBlock->getLayout()
                ->createBlock('Yudiz\SaveForLater\Block\SaveForLater')
                ->setTemplate('Yudiz_SaveForLater::saveforlater.phtml')
                ->toHtml();

            $resultJson->setData(['html' => $html]);
        } catch (\Exception $e) {
            $resultJson->setData(['error' => true, 'message' => $e->getMessage()]);
        }

        return $resultJson;

    }
}