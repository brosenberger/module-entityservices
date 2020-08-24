<?php
/**
 * @author      Benjamin Rosenberger <rosenberger@e-conomix.at>
 * @package BroCode\EntityServices\Model\Schema
 * @copyright Copyright (c) 2020 E-CONOMIX GmbH (https://www.e-conomix.at)
 * @created 23.08.2020
 */

namespace BroCode\EntityServices\Model\Schema;

class ColumnElement extends AbstractElement
{
    protected $columnOptions = [];
    protected $columnSize = null;
    protected $columnName;
    protected $columnType;
    protected $columnComment;

    /**
     * ColumnElement constructor.
     * @param $parent
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     * @param $columnName
     * @param $columnType
     * @param $columnComment
     */
    public function __construct(
        TableElement $parent,
        \Magento\Framework\Setup\SchemaSetupInterface $setup,
        $columnName,
        $columnType,
        $columnComment
    ) {
        parent::__construct($parent, $setup);
        $this->columnName = $columnName;
        $this->columnType = $columnType;
        $this->columnComment = $columnComment;
    }

    public function asPrimaryKey($primary = true)
    {
        $this->columnOptions['primary'] = $primary;
        return $this;
    }

    public function asIdentiy($identity = true)
    {
        $this->columnOptions['identity'] = $identity;
        return $this;
    }

    public function asUnsigned($unsigned = true)
    {
        $this->columnOptions['unsigned'] = $unsigned;
        return $this;
    }

    public function asNullable($nullable = true)
    {
        $this->columnOptions['nullable'] = $nullable;
        return $this;
    }

    public function withSize($size)
    {
        $this->columnSize = $size;
        return $this;
    }

    public function withDefault($value)
    {
        $this->columnOptions['default'] = $value;
        return $this;
    }

    public function withOptions($options)
    {
        $this->columnOptions = array_merge($this->columnOptions, $options);
        return $this;
    }

    protected function getColumnData()
    {
        return [
            $this->columnName,
            $this->columnType,
            $this->columnSize,
            $this->columnOptions,
            $this->columnComment
        ];
    }

    /**
     * @return TableElement
     */
    public function build()
    {
        // todo validate data and register with table
        return $this->parent->registerColumn($this->getColumnData());
    }
}
