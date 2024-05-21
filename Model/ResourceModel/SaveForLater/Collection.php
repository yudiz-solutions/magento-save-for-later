<?php
namespace Yudiz\SaveForLater\Model\ResourceModel\SaveForLater;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'entity_id';
    /**
     * Define resource model.
     */
    protected function _construct()
    {
        $this->_init('Yudiz\SaveForLater\Model\SaveForLater', 'Yudiz\SaveForLater\Model\ResourceModel\SaveForLater');
    }
}