<?php

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
     * @param UrlInterface $url
     */
    public function __construct(
        UrlInterface $url
    ) {
        $this->url = $url;
    }

    /**
     * @param \Magento\Checkout\Block\Cart\Sidebar $subject
     * @param array $result
     * @return array
     */
    public function afterGetConfig(
        \Magento\Checkout\Block\Cart\Sidebar $subject,
        array $result
    ) {
        $result['emptyMiniCart'] = $this->url->getUrl('clearcart/cart/emptycart');
        return $result;
    }
}