<?php
/**
 * @author      Benjamin Rosenberger <rosenberger@e-conomix.at>
 * @package BroCode\EntityServices\Model\Schema
 * @copyright Copyright (c) 2020 E-CONOMIX GmbH (https://www.e-conomix.at)
 * @created 24.08.2020
 */

namespace BroCode\EntityServices\Model\Schema;

/**
 * Class ForeignKeyElement
 * @package BroCode\EntityServices\Model\Schema
 */
class ForeignKeyElement extends AbstractElement
{
    protected $tableName;
    protected $columnName;
    protected $toTable;
    protected $toColumn;
    protected $action = \Magento\Framework\DB\Ddl\Table::ACTION_SET_DEFAULT;
    private $fromColumn;

    /**
     * ForeignKeyElement constructor.
     * @param TableElement $parent
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     * @param $tableName
     */
    public function __construct($parent, \Magento\Framework\Setup\SchemaSetupInterface $setup, $tableName, $fromColumn, $toTable, $toColumn)
    {
        parent::__construct($parent, $setup);
        $this->tableName = $tableName;
        $this->toTable = $toTable;
        $this->toColumn = $toColumn;
        $this->fromColumn = $fromColumn;
    }

    public function fromColumn($columnName)
    {
        $this->columnName = $columnName;
        return $this;
    }
    public function toTable($toTable)
    {
        $this->toTable = $toTable;
        return $this;
    }
    public function toColumn($toColumn)
    {
        $this->toColumn = $toColumn;
        return $this;
    }
    public function action($action)
    {
        $this->action = $action;
        return $this;
    }
    public function actionCascade()
    {
        $this->action(\Magento\Framework\DB\Ddl\Table::ACTION_CASCADE);
        return $this;
    }
    public function actionSetNull()
    {
        $this->action(\Magento\Framework\DB\Ddl\Table::ACTION_SET_NULL);
        return $this;
    }
    public function actionNoAction()
    {
        $this->action(\Magento\Framework\DB\Ddl\Table::ACTION_NO_ACTION);
        return $this;
    }
    public function actionRestrict()
    {
        $this->action(\Magento\Framework\DB\Ddl\Table::ACTION_RESTRICT);
        return $this;
    }
    public function actionSetDefault()
    {
        $this->action(\Magento\Framework\DB\Ddl\Table::ACTION_SET_DEFAULT);
        return $this;
    }

    /**
     * @return TableElement
     */
    public function build()
    {
        $keyName = $this->setup->getFkName(
            $this->tableName,
            $this->fromColumn,
            $this->toTable,
            $this->toColumn
        );

        return $this->parent->registerForeignKey([
            $keyName,
            $this->fromColumn,
            $this->setup->getTable($this->toTable),
            $this->toColumn,
            $this->action
        ]);
    }
}
