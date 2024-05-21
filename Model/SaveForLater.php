<?php
namespace Yudiz\SaveForLater\Model;

use Yudiz\SaveForLater\Api\Data\SaveForLaterInterface;
use Magento\Framework\Model\AbstractModel;
use Yudiz\SaveForLater\Model\ResourceModel\SaveForLater as SaveForLaterResourceModel;

class SaveForLater extends AbstractModel implements SaveForLaterInterface
{
    /**
     * CMS page cache tag.
     */
    const CACHE_TAG = 'yudiz_saveforlater';

    /**
     * @var string
     */
    protected $_cacheTag = 'yudiz_saveforlater';

    /**
     * Prefix of model events names.
     *
     * @var string
     */
    protected $_eventPrefix = 'yudiz_saveforlater';

    /**
     * Initialize resource model.
     */
    protected function _construct()
    {
        $this->_init(SaveForLaterResourceModel::class);
    }

    /**
     * Get ID.
     *
     * @return int
     */
    public function getId()
    {
        return $this->getData(self::ID);
    }

    /**
     * Set ID.
     */
    public function setId($id)
    {
        return $this->setData(self::ID, $id);
    }

    /**
     * Get User ID.
     *
     * @return int
     */
    public function getUserId()
    {
        return $this->getData(self::USER_ID);
    }

    /**
     * Set User ID.
     */
    public function setUserId($userId)
    {
        return $this->setData(self::USER_ID, $userId);
    }

    /**
     * Get Product ID.
     *
     * @return int
     */
    public function getProductId()
    {
        return $this->getData(self::PRODUCT_ID);
    }

    /**
     * Set Product ID.
     */
    public function setProductId($productId)
    {
        return $this->setData(self::PRODUCT_ID, $productId);
    }

    /**
     * Get Quantity.
     *
     * @return int
     */
    public function getQty()
    {
        return $this->getData(self::QTY);
    }

    /**
     * Set Quantity.
     */
    public function setQty($qty)
    {
        return $this->setData(self::QTY, $qty);
    }

    /**
     * Get Created At.
     *
     * @return string
     */
    public function getCreatedAt()
    {
        return $this->getData(self::CREATED_AT);
    }

    /**
     * Set Created At.
     */
    public function setCreatedAt($createdAt)
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }

    /**
     * Get Updated At.
     *
     * @return string
     */
    public function getUpdatedAt()
    {
        return $this->getData(self::UPDATED_AT);
    }

    /**
     * Set Updated At.
     */
    public function setUpdatedAt($updatedAt)
    {
        return $this->setData(self::UPDATED_AT, $updatedAt);
    }
}