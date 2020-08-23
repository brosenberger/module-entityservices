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
 * @package BroCode\EntityServices\Model\Schema
 */
class TableElement extends AbstractElement
{
    protected $tableName;
    protected $tableComment;

    protected $columns = [];
    protected $index = [];

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
    public function withIntColumn($columnName, $comment='')
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
    public function withDecimalColumn($columnName, $comment ='')
    {
        return $this->withColumn(
            $columnName,
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            $comment
        )->withSize('12,4');
    }
    public function withDateTimeColumn($columnName, $comment='')
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

    public function withIndex($columns, $options = [])
    {
        return new IndexElement($this, $this->setup, $this->tableName, $columns, $options);
    }

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

    public function build()
    {
        $table = $this->setup->getConnection()
            ->newTable($this->setup->getTable($this->tableName));

        // add columns
        foreach ($this->columns as $columData) {
            $table->addColumn(...$columData);
        }

        // add indizes
        foreach ($this->index as $indexData) {
            $table->addIndex(...$indexData);
        }

        // add foreign keys

        // add comment
        $table->setComment($this->tableComment);

        // persist in db
        $this->setup->getConnection()->createTable($table);

        // TODO  build table with configurations
        return $this->parent;
    }
}
