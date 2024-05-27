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

namespace Yudiz\SaveForLater\Plugin\Cart;

use Magento\Framework\UrlInterface;

class ConfigPlugin
{
    /**
     * @var UrlInterface
     */
    protected $url;

    /**
     * ConfigPlugin constructor.
     *
     * @param UrlInterface $url The URL interface
     */
    public function __construct(
        UrlInterface $url
    ) {
        $this->url = $url;
    }

    /**
     * After get config
     *
     * @param \Magento\Checkout\Block\Cart\Sidebar $subject The sidebar block
     * @param array $result The result array
     * @return array
     */
    public function afterGetConfig(
        \Magento\Checkout\Block\Cart\Sidebar $subject,
        array $result
    ) {
        $result['emptyMiniCart'] = $this->url->getUrl('saveforlater/cart/emptycart');
        return $result;
    }
}
