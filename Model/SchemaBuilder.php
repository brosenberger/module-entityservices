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
}
