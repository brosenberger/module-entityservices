<?php
/**
 * @author      Benjamin Rosenberger <rosenberger@e-conomix.at>
 * @package BroCode\EntityServices\Model\Schema
 * @copyright Copyright (c) 2020 E-CONOMIX GmbH (https://www.e-conomix.at)
 * @created 23.08.2020
 */

namespace BroCode\EntityServices\Model\Schema;

use BroCode\EntityServices\Model\SchemaBuilder;

/**
 * Class TableElement
 * .
 */
class TableElement extends AbstractElement
{
    const CALLBACK_AFTERTABLECREATE = 'after_table_create';

    protected $tableName;
    protected $tableComment;

    protected $columns = [];
    protected $index = [];
    protected $foreignKey = [];
    protected $callbacks = [];

    /**
     * TableElement constructor.
     * @param SchemaBuilder $parent
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     * @param string $tableName
     * @param string $comment
     */
    public function __construct(
        SchemaBuilder $parent,
        \Magento\Framework\Setup\SchemaSetupInterface $setup,
        $tableName,
        $comment = ''
    ) {
        parent::__construct($parent, $setup);
        $this->parent = $parent;
        $this->tableName = $tableName;
        $this->tableComment = empty($comment) ? $tableName : $comment;
    }

    // ------------- column methods

    public function withColumn($columnName, $type, $comment = '')
    {
        return new ColumnElement(
            $this,
            $this->setup,
            $columnName,
            $type,
            empty($comment) ? $columnName : $comment
        );
    }
    public function withTextColumn($columnName, $comment = '')
    {
        return $this->withColumn(
            $columnName,
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            $comment
        )->withSize('64k');
    }
    public function withVarcharColumn($columnName, $comment = '')
    {
        return $this->withColumn(
            $columnName,
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            $comment
        )->withSize(255);
    }
    public function withIntColumn($columnName, $comment = '')
    {
        return $this->withColumn(
            $columnName,
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            $comment
        );
    }
    public function withSmallIntColumn($columnName, $comment = '')
    {
        return $this->withColumn(
            $columnName,
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            $comment
        );
    }
    public function withDecimalColumn($columnName, $comment = '')
    {
        return $this->withColumn(
            $columnName,
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            $comment
        )->withSize('12,4');
    }
    public function withDateTimeColumn($columnName, $comment = '')
    {
        return $this->withColumn(
            $columnName,
            \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
            $comment
        );
    }
    public function withTimestampColumn($columnName, $comment = '')
    {
        return $this->withColumn(
            $columnName,
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            $comment
        );
    }
    public function withBooleanColumn($columnName, $comment = '')
    {
        return $this->withColumn(
            $columnName,
            \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
            $comment
        )->withSize(1);
    }

    public function registerColumn(array $columnData)
    {
        $this->columns[] = $columnData;
        return $this;
    }

    // ------------- index methods

    /**
     * @param array|string $columns
     * @param array $options
     * @return IndexElement
     */
    public function withIndex($columns, $options = [])
    {
        return new IndexElement($this, $this->setup, $this->tableName, is_array($columns)?$columns:[$columns], $options);
    }

    /**
     * @param array|string $columns
     * @param array $options
     * @return IndexElement
     */
    public function withUniqueIndex($columns, $options = [])
    {
        return $this->withIndex(
            $columns,
            array_merge(
                $options,
                ['type'=>\Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE]
            )
        );
    }

    public function registerIndex(array $indexData)
    {
        $this->index[] = $indexData;
        return $this;
    }

    // -------- foreign keys

    public function withForeignKey($fromColumn, $toTable, $toColumn)
    {
        return new ForeignKeyElement($this, $this->setup, $this->tableName, $fromColumn, $toTable, $toColumn);
    }

    public function registerForeignKey(array $foreignKeyData) {
        $this->foreignKey[] = $foreignKeyData;
        return $this;
    }

    public function registerCallback($position, callable $callback)
    {
        $this->callbacks[$position][] = $callback;
        return $this;
    }

    public function build()
    {
        $table = $this->createTable();

        $this->processColumns($table);
        $this->processIndizes($table);
        $this->processForeignKeys($table);
        $this->processAdditionals($table);

        $this->persistTable($table);

        $this->callCallbacks(self::CALLBACK_AFTERTABLECREATE);

        return $this->parent;
    }

    /**
     * @return \Magento\Framework\DB\Ddl\Table
     */
    protected function createTable()
    {
        return $this->setup->getConnection()->newTable($this->setup->getTable($this->tableName));
    }

    /**
     * @param \Magento\Framework\DB\Ddl\Table|null $table
     */
    protected function processColumns($table = null) {
        if ($table === null) {
            return;
        }
        foreach ($this->columns as $columData) {
            $table->addColumn(...$columData);
        }
    }

    /**
     * @param \Magento\Framework\DB\Ddl\Table|null $table
     */
    protected function processIndizes($table = null) {
        if ($table === null) {
            return;
        }
        foreach ($this->index as $indexData) {
            $table->addIndex(...$indexData);
        }
    }

    /**
     * @param \Magento\Framework\DB\Ddl\Table|null $table
     */
    protected function processForeignKeys($table = null) {
        if ($table === null) {
            return;
        }
        foreach ($this->foreignKey as $foreignKeyData) {
            $table->addForeignKey(...$foreignKeyData);
        }
    }

    /**
     * @param \Magento\Framework\DB\Ddl\Table|null $table
     */
    protected function processAdditionals($table = null) {
        if ($table === null) {
            return;
        }
        $table->setComment($this->tableComment);
    }

    /**
     * @param \Magento\Framework\DB\Ddl\Table|null $table
     * @throws \Zend_Db_Exception
     */
    protected function persistTable($table = null) {
        if ($table === null) {
            return;
        }
        $this->setup->getConnection()->createTable($table);
    }

    protected function callCallbacks($position)
    {
        if (isset($this->callbacks[$position]) && count($this->callbacks[$position]) > 0) {
            foreach ($this->callbacks[$position] as $callback) {
                $callback();
            }
        }
    }
}
