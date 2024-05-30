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

namespace Yudiz\SaveForLater\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Yudiz\SaveForLater\Model\ResourceModel\SaveForLater\CollectionFactory;
use Magento\Framework\App\ResourceConnection;

class AnalyzeChart extends Template
{
    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var ResourceConnection
     */
    protected $resourceConnection;

    /**
     * AnalyzeChart constructor.
     *
     * @param Context $context
     * @param CollectionFactory $collectionFactory
     * @param ResourceConnection $resourceConnection
     * @param array $data
     */
    public function __construct(
        Context $context,
        CollectionFactory $collectionFactory,
        ResourceConnection $resourceConnection,
        array $data = []
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->resourceConnection = $resourceConnection;
        parent::__construct($context, $data);
    }

    /**
     * Get customer analysis data per day for the last 7 days.
     *
     * @return array
     */
    public function getCustomerAnalyzeDataPerDay()
    {
        $connection = $this->resourceConnection->getConnection();
        $tableName = $this->resourceConnection->getTableName('yudiz_save_for_later');

        $select = $connection->select()
            ->from($tableName, ['DATE(created_at) AS created_date', 'COUNT(DISTINCT user_id) AS customer_count'])
            ->where('DATE(created_at) >= DATE(DATE_SUB(CURDATE(), INTERVAL 7 DAY))')
            ->group('created_date')
            ->order('created_date DESC')
            ->limit(7);

        return $connection->fetchAll($select);
    }

    /**
     * Get the most saved products by customers.
     *
     * @return array
     */
    public function getMostSavedProductsByCustomers()
    {
        $connection = $this->resourceConnection->getConnection();
        $saveForLaterTableName = $this->resourceConnection->getTableName('yudiz_save_for_later');
        $productTableName = $this->resourceConnection->getTableName('catalog_product_entity');
        $productVarcharTableName = $this->resourceConnection->getTableName('catalog_product_entity_varchar');
        $eavAttributeTableName = $this->resourceConnection->getTableName('eav_attribute');

        $select = $connection->select()
            ->from(
                ['sfl' => $saveForLaterTableName],
                ['product_id', 'COUNT(DISTINCT user_id) AS customer_count']
            )
            ->join(
                ['p' => $productTableName],
                'sfl.product_id = p.entity_id',
                []
            )
            ->join(
                ['pv' => $productVarcharTableName],
                'p.entity_id = pv.entity_id',
                ['product_name' => 'pv.value']
            )
            ->join(
                ['ea' => $eavAttributeTableName],
                'pv.attribute_id = ea.attribute_id AND ea.attribute_code = "name"',
                []
            )
            ->group('sfl.product_id')
            ->order('customer_count DESC')
            ->limit(10);

        return $connection->fetchAll($select);
    }

    /**
     * Get monthly trends data for the last 7 months.
     *
     * @return array
     */
    public function getMonthlyTrendsData()
    {
        $connection = $this->resourceConnection->getConnection();
        $tableName = $this->resourceConnection->getTableName('yudiz_save_for_later');

        $select = $connection->select()
            ->from(
                $tableName,
                [
                    'month' => 'DATE_FORMAT(created_at, "%Y-%m")',
                    'save_count' => 'COUNT(*)'
                ]
            )
            ->where('created_at >= DATE_SUB(NOW(), INTERVAL 7 MONTH)')
            ->group('month')
            ->order('month DESC')
            ->limit(10);

        return $connection->fetchAll($select);
    }

    /**
     * Get yearly data for the last 6 years.
     *
     * @return array
     */
    public function getYearlyData()
    {
        $connection = $this->resourceConnection->getConnection();
        $tableName = $this->resourceConnection->getTableName('yudiz_save_for_later');

        $select = $connection->select()
            ->from(
                $tableName,
                [
                    'year' => new \Zend_Db_Expr('YEAR(created_at)'),
                    'customer_count' => new \Zend_Db_Expr('COUNT(DISTINCT user_id)')
                ]
            )
            ->where('created_at >= DATE_SUB(NOW(), INTERVAL 6 YEAR)')
            ->group('year')
            ->order('year DESC')
            ->limit(6);

        return $connection->fetchAll($select);
    }
}
