<?php
/**
 * @author      Benjamin Rosenberger <rosenberger@e-conomix.at>
 * @package BroCode\EntityServices\Model
 * @copyright Copyright (c) 2020 E-CONOMIX GmbH (https://www.e-conomix.at)
 * @created 24.08.2020
 */

namespace BroCode\EntityServices\Model;

/**
 * Class SchemaBuilderFactory
 * @package BroCode\EntityServices\Model
 */
class SchemaBuilderFactory
{
    /**
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     * @return SchemaBuilder
     */
    public function create(\Magento\Framework\Setup\SchemaSetupInterface $setup) {
        return new SchemaBuilder($setup);
    }
}