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

namespace Yudiz\SaveForLater\Block\Adminhtml\Edit\Tab;

use Magento\Customer\Controller\RegistryConstants;
use Magento\Ui\Component\Layout\Tabs\TabInterface;

/**
 * Class SaveForLaterTab
 * This class defines a custom tab in the customer edit section for "Save For Later" functionality.
 */
class SaveForLaterTab extends \Magento\Backend\Block\Template implements TabInterface
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * SaveForLaterTab constructor.
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * Get tab label
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('Save For Later');
    }

    /**
     * Get tab title
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Save For Later');
    }

    /**
     * Check if tab can be shown
     *
     * @return bool
     */
    public function canShowTab()
    {
        $customerId = $this->getCustomerId();
        return $customerId !== null;
    }

    /**
     * Get customer ID
     *
     * @return int|null
     */
    public function getCustomerId()
    {
        return $this->_coreRegistry->registry(RegistryConstants::CURRENT_CUSTOMER_ID);
    }

    /**
     * Check if tab is hidden
     *
     * @return bool
     */
    public function isHidden()
    {
        $customerId = $this->getCustomerId();
        return $customerId === null;
    }

    /**
     * Get tab class
     *
     * @return string
     */
    public function getTabClass()
    {
        return '';
    }

    /**
     * Get tab URL
     *
     * @return string
     */
    public function getTabUrl()
    {
        return $this->getUrl('saveforlater/*/customertab', ['_current' => true]);
    }

    /**
     * Check if tab is loaded via AJAX
     *
     * @return bool
     */
    public function isAjaxLoaded()
    {
        return true;
    }
}
