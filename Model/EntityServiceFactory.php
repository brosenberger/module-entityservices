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
     * @depecated use method createAttributeBuilder
     */
    public function createProductAttributeBuilder(\Magento\Framework\Setup\ModuleDataSetupInterface $setup)
    {
        return $this->createAttributeBuilder($setup);
    }

    public function createAttributeBuilder(\Magento\Framework\Setup\ModuleDataSetupInterface $setup)
    {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
        return new AttributeBuilder($eavSetup, $this->attributeResourceModel, $this->attributeRepository);
    }

    /**
     * @param \Magento\Framework\Setup\ModuleContextInterface $context
     * @param string $versionToBe
     * @return bool
     */
    public function versionLessThan($context, $versionToBe)
    {
        return version_compare($context->getVersion(), '0.0.2') < 0;
    }
}
