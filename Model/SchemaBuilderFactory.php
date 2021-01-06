<?php
/**
 * @author      Benjamin Rosenberger <rosenberger@e-conomix.at>
 * @copyright Copyright (c) 2020 E-CONOMIX GmbH (https://www.e-conomix.at)
 * @created 24.08.2020
 */

namespace BroCode\EntityServices\Model;

/**
 * Class SchemaBuilderFactory .
 * Please use other class, this name is misleading as also eav attributes can be added/changed
 * @depecated use \BroCode\EntityServices\Model\EntityServiceFactory
 */
class SchemaBuilderFactory
{
    /**
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     * @return SchemaBuilder
     * @depecated use \BroCode\EntityServices\Model\ServiceFactory::createSchemaBuilder
     */
    public function create(\Magento\Framework\Setup\SchemaSetupInterface $setup)
    {
        return new SchemaBuilder($setup);
    }
}
