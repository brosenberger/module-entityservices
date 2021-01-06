<?php
/**
 * @author      Benjamin Rosenberger <rosenberger@e-conomix.at>
 * @package BroCode\EntityServices\Model\Attribute
 * @copyright Copyright (c) 2021 E-CONOMIX GmbH (https://www.e-conomix.at)
 * @created 04.01.2021
 */

namespace BroCode\EntityServices\Model;

use BroCode\EntityServices\Api\ElementInterface;
use BroCode\EntityServices\Model\Attribute\AttributeElement;
use BroCode\EntityServices\Model\Attribute\CustomerAddressAttributeElement;
use BroCode\EntityServices\Model\Attribute\CustomerAttributeElement;
use BroCode\EntityServices\Model\Attribute\ProductAttributeElement;
use Magento\Catalog\Model\Category;
use Magento\Customer\Model\ResourceModel\Attribute;
use Magento\Eav\Api\AttributeRepositoryInterface;

/**
 * Class AttributeBuilder
 * @package BroCode\EntityServices\Model\Attribute
 */
class AttributeBuilder implements ElementInterface
{
    /**
     * @var \Magento\Eav\Setup\EavSetup
     */
    protected $eavSetup;
    /**
     * @var Attribute
     */
    private $attributeResourceModel;
    /**
     * @var AttributeRepositoryInterface
     */
    private $attributeRepository;

    /**
     * AttributeBuilder constructor.
     * @param \Magento\Eav\Setup\EavSetup $eavSetup
     */
    public function __construct(
        \Magento\Eav\Setup\EavSetup $eavSetup,
        Attribute $attributeResourceModel,
        AttributeRepositoryInterface $attributeRepository
    ) {
        $this->eavSetup = $eavSetup;
        $this->attributeResourceModel = $attributeResourceModel;
        $this->attributeRepository = $attributeRepository;
    }

    public function build()
    {
        return $this;
    }

    public function withProductAttribute($attributeCode)
    {
        return new ProductAttributeElement($this->eavSetup, $this, $attributeCode);
    }

    public function withCustomerAttribute($attributeCode, $defaultValues = true)
    {
        $customerAttribute = new CustomerAttributeElement(
            $this->eavSetup,
            $this->attributeResourceModel,
            $this->attributeRepository,
            $this,
            $attributeCode
        );
        if ($defaultValues === true) {
            $customerAttribute->withDefaults();
        }
        return $customerAttribute;
    }

    public function withCustomerAddressAttribute($attributeCode, $defaultValues = true)
    {
        $customerAddressAttribute = new CustomerAddressAttributeElement(
            $this->eavSetup,
            $this->attributeResourceModel,
            $this->attributeRepository,
            $this,
            $attributeCode
        );
        if ($defaultValues === true) {
            $customerAddressAttribute->withDefaults();
        }
        return $customerAddressAttribute;
    }

    public function withCategoryAttribute($attributeCode)
    {
        return new AttributeElement($this->eavSetup, $this, Category::ENTITY, $attributeCode);
    }

    public function withGenericAttribute($entityTypeId, $attributeCode)
    {
        return new AttributeElement($this->eavSetup, $this, $entityTypeId, $attributeCode);
    }
}
