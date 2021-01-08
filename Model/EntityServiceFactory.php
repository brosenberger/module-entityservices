<?php
/**
 * @author      Benjamin Rosenberger <rosenberger@e-conomix.at>
 * @copyright Copyright (c) 2021 E-CONOMIX GmbH (https://www.e-conomix.at)
 * @created 04.01.2021
 */

namespace BroCode\EntityServices\Model;

use Magento\Customer\Model\ResourceModel\Attribute;
use Magento\Eav\Api\AttributeRepositoryInterface;

/**
 * Class ServiceFactory
 * .
 */
class EntityServiceFactory
{
    /**
     * @var Attribute
     */
    private $attributeResourceModel;
    /**
     * @var AttributeRepositoryInterface
     */
    private $attributeRepository;
    /**
     * @var \Magento\Eav\Setup\EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * EntityServiceFactory constructor.
     * @param Attribute $attributeResourceModel
     * @param AttributeRepositoryInterface $attributeRepository
     */
    public function __construct(
        Attribute $attributeResourceModel,
        AttributeRepositoryInterface $attributeRepository,
        \Magento\Eav\Setup\EavSetupFactory $eavSetupFactory
    ) {
        $this->attributeResourceModel = $attributeResourceModel;
        $this->attributeRepository = $attributeRepository;
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     * @return SchemaBuilder
     */
    public function createSchemaBuilder(\Magento\Framework\Setup\SchemaSetupInterface $setup)
    {
        return new SchemaBuilder($setup);
    }

    /**
     * @param \Magento\Eav\Setup\EavSetup $eavSetup
     * @return AttributeBuilder
     */
    public function createProductAttributeBuilder(\Magento\Framework\Setup\ModuleDataSetupInterface $setup)
    {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
        return new AttributeBuilder($eavSetup, $this->attributeResourceModel, $this->attributeRepository);
    }
}
