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

namespace Yudiz\SaveForLater\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultFactory;

/**
 * Class CustomerTab
 *
 * Controller action for rendering the customer tab layout in the admin panel.
 */
class CustomerTab extends Action
{
    /**
     * Execute the action to create and return a layout result
     *
     * @return \Magento\Framework\View\Result\Layout
     */
    public function execute()
    {
        $resultLayout = $this->resultFactory->create(ResultFactory::TYPE_LAYOUT);
        return $resultLayout;
    }
}
