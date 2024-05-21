<?php

namespace Yudiz\SaveForLater\Api\Data;

interface SaveForLaterInterface
{
    /**
     * Constants for keys of data array. Identical to the name of the getter in snake case.
     */
    const ID = 'entity_id';
    const USER_ID = 'user_id';
    const PRODUCT_ID = 'product_id';
    const QTY = 'qty';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    /**
     * Get ID.
     *
     * @return int
     */
    public function getId();

    /**
     * Set ID.
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
     */
    public function setUpdatedAt($updatedAt);
}