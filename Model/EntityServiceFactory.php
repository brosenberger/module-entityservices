<?php
/**
 * @author      Benjamin Rosenberger <rosenberger@e-conomix.at>
 * @package BroCode\EntityServices\Model
 * @copyright Copyright (c) 2021 E-CONOMIX GmbH (https://www.e-conomix.at)
 * @created 04.01.2021
 */

namespace BroCode\EntityServices\Model;

/**
 * Class ServiceFactory
 * @package BroCode\EntityServices\Model
 */
class EntityServiceFactory
{
    /**
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     * @return SchemaBuilder
     */
    public function createSchemaBuilder(\Magento\Framework\Setup\SchemaSetupInterface $setup)
    {
        return new SchemaBuilder($setup);
    }

    public function createProductAttributeBuilder(\Magento\Eav\Setup\EavSetup $eavSetup)
    {
        return new AttributeBuilder($eavSetup);
    }
}
