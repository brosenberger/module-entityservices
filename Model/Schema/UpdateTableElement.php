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

    /**
     * @param string $fk
     * @return $this
     */
    public function dropForeignKey($fk)
    {
        $this->dropFk[] = $fk;
        return $this;
    }

    /**
     * @param string $index
     * @return $this
     */
    public function dropIndex($index)
    {
        $this->dropIndex[] = $index;
        return $this;
    }

    /**
     * @param string $from
     * @param string $to
     * @param array|null $definition
     * @return $this
     */
    public function renameColumn($from, $to, $definition = null) {
        $tableName = $this->setup->getConnection()->getTableName($this->tableName);
        if ($definition == null) {
            $ddlDefinition = $this->setup->getConnection()->describeTable(
                $tableName
            )[$from];
            $definition = [
                'type' => $ddlDefinition['DATA_TYPE'],
                'default' => $ddlDefinition['DEFAULT'],
                'nullable' => $ddlDefinition['NULLABLE'],
                'length' => $ddlDefinition['LENGTH'],
                'primary' => $ddlDefinition['PRIMARY'],
                'identity' => $ddlDefinition['IDENTITY'],
                'unsigned' => $ddlDefinition['UNSIGNED'],
                'precision' => $ddlDefinition['PRECISION'],
                'scale' => $ddlDefinition['SCALE'],
            ];
        }
        $this->setup->getConnection()->changeColumn(
            $tableName,
            $from,
            $to,
            $definition
        );
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
