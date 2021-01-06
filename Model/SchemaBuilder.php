<?php
/**
 * @author      Benjamin Rosenberger <rosenberger@e-conomix.at>
 * @copyright Copyright (c) 2020 E-CONOMIX GmbH (https://www.e-conomix.at)
 * @created 23.08.2020
 */

namespace BroCode\EntityServices\Model;

use BroCode\EntityServices\Api\ElementInterface;
use BroCode\EntityServices\Model\Schema\TableElement;
use BroCode\EntityServices\Model\Schema\UpdateTableElement;

/**
 * Class SchemaBuilder
 * .
 */
class SchemaBuilder implements ElementInterface
{
    /**
     * @var \Magento\Framework\Setup\SchemaSetupInterface
     */
    private $setup;

    /**
     * SchemaBuilder constructor.
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     */
    public function __construct(\Magento\Framework\Setup\SchemaSetupInterface $setup)
    {
        $this->setup = $setup;
    }

    /**
     * @param string $tableName
     * @param string $comment
     * @return UpdateTableElement
     */
    public function updateTable($tableName)
    {
        if (!$this->setup->tableExists($this->setup->getTable($tableName))) {
            throw new \RuntimeException('Update table ' . $this->setup->getTable($tableName) . ' does not exist!');
        }
        return $this->withTable($tableName);
    }

    /**
     * @param string $tableName
     * @param string $comment
     * @return TableElement|UpdateTableElement
     */
    public function withTable($tableName, $comment = '')
    {
        if (!$this->setup->tableExists($this->setup->getTable($tableName))) {
            return new TableElement($this, $this->setup, $tableName, $comment);
        } else {
            return new UpdateTableElement($this, $this->setup, $tableName, $comment);
        }
    }

    public function buildEavEntity($tableName, $comment = '')
    {
        $eavTable = $this->withTable($tableName, $comment)
            ->withIntColumn('entity_id')->asIdentiy()->asNullable(false)->asPrimaryKey()->asUnsigned()->build();
        $eavTable->registerCallback(
            TableElement::CALLBACK_AFTERTABLECREATE,
            function () use ($tableName) {
                // add attribute tables
                $this->buildEavAttributeTable($tableName, $this->withTable($tableName . '_datetime'), 'withDateTimeColumn')
                    ->buildEavAttributeTable($tableName, $this->withTable($tableName . '_decimal'), 'withDecimalColumn')
                    ->buildEavAttributeTable($tableName, $this->withTable($tableName . '_int'), 'withIntColumn')
                    ->buildEavAttributeTable($tableName, $this->withTable($tableName . '_text'), 'withTextColumn')
                    ->buildEavAttributeTable($tableName, $this->withTable($tableName . '_varchar'), 'withVarcharColumn');
                return;
            }
        );
        return $eavTable;
    }

    public function buildEavAttributeTable($baseTable, TableElement $table, $valueColumnMethod, $options = [])
    {
        // add columns
        $table->withIntColumn('value_id')->asIdentiy()->asNullable(false)->asPrimaryKey()->build()
            ->withSmallIntColumn('attribute_id')->asUnsigned()->asNullable(false)->withDefault(0)->build()
            ->withSmallIntColumn('store_id')->asUnsigned()->asNullable(false)->withDefault(0)->build()
            ->withIntColumn('entity_id')->asUnsigned()->asNullable(false)->withDefault(0)->build()
            ->{$valueColumnMethod}('value_id')->withOptions($options)->build();

        // add indizes
        $table->withUniqueIndex(['entity_id', 'attribute_id', 'store_id'])->build()
            ->withIndex(['attribute_id'])->build()
            ->withIndex(['store_id'])->build();

        // add foreign keys
        $table->withForeignKey('attribute_id', 'eav_attribute', 'attribute_id')->actionCascade()->build()
            ->withForeignKey('entity_id', $baseTable, 'entity_id')->actionCascade()->build()
            ->withForeignKey('store_id', 'store', 'store_id')->actionCascade()->build();

        return $table->build();
    }

    public function build()
    {
        // nothing to do
        return $this;
    }
}
