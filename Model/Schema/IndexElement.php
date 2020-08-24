<?php
/**
 * @author      Benjamin Rosenberger <rosenberger@e-conomix.at>
 * @package BroCode\EntityServices\Model\Schema
 * @copyright Copyright (c) 2020 E-CONOMIX GmbH (https://www.e-conomix.at)
 * @created 23.08.2020
 */

namespace BroCode\EntityServices\Model\Schema;

class IndexElement extends AbstractElement
{
    /**
     * @var array
     */
    private $columns;
    /**
     * @var array
     */
    private $options;
    private $tableName;

    /**
     * IndexElement constructor.
     * @param TableElement $parent
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     * @param array $columns
     * @param array $options
     */
    public function __construct(
        TableElement $parent,
        \Magento\Framework\Setup\SchemaSetupInterface $setup,
        $tableName,
        array $columns,
        array $options
    ) {
        parent::__construct($parent, $setup);
        $this->columns = $columns;
        $this->options = $options;
        $this->tableName = $tableName;
    }

    public function build()
    {
        $indexName = $this->setup->getIdxName(
            $this->tableName,
            $this->columns,
            isset($this->options['type']) ? $this->options['type'] : ''
        );
        return $this->parent->registerIndex([
            $indexName,
            $this->columns,
            $this->options
        ]);
    }
}
