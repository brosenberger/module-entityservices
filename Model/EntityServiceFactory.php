<?php
/**
 * @author      Benjamin Rosenberger <rosenberger@e-conomix.at>
 * @copyright Copyright (c) 2021 E-CONOMIX GmbH (https://www.e-conomix.at)
 * @created 04.01.2021
 */

namespace BroCode\EntityServices\Model;

use BroCode\EntityServices\Model\Schema\SalesEntityAttributeElement;
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
     * @var \Magento\Sales\Setup\SalesSetupFactory
     */
    private $salesSetupFactory;
    /**
     * @var \Magento\Quote\Setup\QuoteSetupFactory
     */
    private $quoteSetupFactory;

    /**
     * EntityServiceFactory constructor.
     * @param Attribute $attributeResourceModel
     * @param AttributeRepositoryInterface $attributeRepository
     */
    public function __construct(
        Attribute $attributeResourceModel,
        AttributeRepositoryInterface $attributeRepository,
        \Magento\Eav\Setup\EavSetupFactory $eavSetupFactory,
        \Magento\Sales\Setup\SalesSetupFactory $salesSetupFactory,
        \Magento\Quote\Setup\QuoteSetupFactory $quoteSetupFactory
    ) {
        $this->attributeResourceModel = $attributeResourceModel;
        $this->attributeRepository = $attributeRepository;
        $this->eavSetupFactory = $eavSetupFactory;
        $this->salesSetupFactory = $salesSetupFactory;
        $this->quoteSetupFactory = $quoteSetupFactory;
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
        return version_compare($context->getVersion(), $versionToBe) < 0;
    }

    public function createSalesEntityAttribute(\Magento\Framework\Setup\ModuleDataSetupInterface $setup)
    {
        return new SalesEntityAttributeElement($setup, $this->salesSetupFactory, $this->quoteSetupFactory, $this);
    }
}
