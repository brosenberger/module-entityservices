<?php
/**
 * @author      Benjamin Rosenberger <rosenberger@e-conomix.at>
 * @copyright Copyright (c) 2020 E-CONOMIX GmbH (https://www.e-conomix.at)
 * @created 28.12.2020
 */

namespace BroCode\EntityServices\Model\Schema;

/**
 * Class UpdateTableElement
 *
 * Currently only add column, modify column is supported
 */
class UpdateTableElement extends TableElement
{
    protected $dropFk = [];
    protected $dropIndex = [];

    public function dropForeignKey($fk)
    {
        $this->dropFk[] = $fk;
        return $this;
    }
    public function dropIndex($index)
    {
        $this->dropIndex[] = $index;
        return $this;
    }

    protected function createTable()
    {
        return null;
    }

    protected function processAdditionals($table = null)
    {
        parent::processAdditionals($table);

        foreach ($this->dropFk as $foreignKey) {
            $this->setup->getConnection()->dropForeignKey($this->setup->getTable($this->tableName), $foreignKey);
        }
        foreach ($this->dropIndex as $index) {
            $this->setup->getConnection()->dropIndex($this->setup->getTable($this->tableName), $index);
        }
    }

    protected function processColumns($table = null)
    {
        $connection = $this->setup->getConnection();
        $tableName = $this->setup->getTable($this->tableName);
        foreach ($this->columns as $column) {
            $columnConfiguration = $this->prepareColumnData($tableName, $column);

            if ($this->setup->getConnection()->tableColumnExists($tableName, $columnConfiguration[1])) {
                $connection->modifyColumn(... $columnConfiguration);
            } else {
                $connection->addColumn(... $columnConfiguration);
            }
        }
    }

    protected function prepareColumnData($tableName, $column)
    {
        list($columnName, $columnType, $columnSize, $columnOptions, $columnComment) = $column;
        return [
                $tableName,
                $columnName,
                array_merge([
                    'type' => $columnType,
                    'length' => $columnSize,
                    'comment' => $columnComment
                ], $columnOptions)
            ];
    }
}
