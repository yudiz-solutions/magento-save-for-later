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

namespace Yudiz\SaveForLater\Api\Data;

interface SaveForLaterInterface
{
    /**
     * Constants for keys of data array. Identical to the name of the getter in snake case.
     */
    public const ID = 'entity_id';
    public const USER_ID = 'user_id';
    public const PRODUCT_ID = 'product_id';
    public const QTY = 'qty';
    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = 'updated_at';

    /**
     * Get ID.
     *
     * @return int
     */
    public function getId();

    /**
     * Set ID.
     *
     * @param int $id
     * @return void
     */
    public function setId($id);

    /**
     * Get User ID.
     *
     * @return int
     */
    public function getUserId();

    /**
     * Set User ID.
     *
     * @param int $userId
     * @return void
     */
    public function setUserId($userId);

    /**
     * Get Product ID.
     *
     * @return int
     */
    public function getProductId();

    /**
     * Set Product ID.
     *
     * @param int $productId
     * @return void
     */
    public function setProductId($productId);

    /**
     * Get Quantity.
     *
     * @return int
     */
    public function getQty();

    /**
     * Set Quantity.
     *
     * @param int $qty
     * @return void
     */
    public function setQty($qty);

    /**
     * Get Created At.
     *
     * @return string
     */
    public function getCreatedAt();

    /**
     * Set Created At.
     *
     * @param string $createdAt
     * @return void
     */
    public function setCreatedAt($createdAt);

    /**
     * Get Updated At.
     *
     * @return string
     */
    public function getUpdatedAt();

    /**
     * Set Updated At.
     *
     * @param string $updatedAt
     * @return void
     */
    public function setUpdatedAt($updatedAt);
}
