<?php
/**
 * @author      Benjamin Rosenberger <rosenberger@e-conomix.at>
 * @package BroCode\EntityServices\Model
 * @copyright Copyright (c) 2020 E-CONOMIX GmbH (https://www.e-conomix.at)
 * @created 23.08.2020
 */

namespace BroCode\EntityServices\Model;

use BroCode\EntityServices\Model\Schema\TableElement;

/**
 * Class SchemaBuilder
 * @package BroCode\EntityServices\Model
 */
class SchemaBuilder
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

    public function withTable($tableName, $comment = '')
    {
        return new TableElement($this, $this->setup, $tableName, $comment);
    }

    public function buildEavEntity($tableName, $comment = '')
    {
        $eavTable = $this->withTable($tableName, $comment);
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
}
