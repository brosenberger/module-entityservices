<?php
/**
 * @author      Benjamin Rosenberger <rosenberger@e-conomix.at>
 * @package BroCode\EntityServices\Model\Attribute
 * @copyright Copyright (c) 2021 E-CONOMIX GmbH (https://www.e-conomix.at)
 * @created 04.01.2021
 */

namespace BroCode\EntityServices\Model\Attribute;

use BroCode\EntityServices\Api\ElementInterface;
use Magento\Customer\Model\ResourceModel\Attribute;
use Magento\Eav\Api\AttributeRepositoryInterface;

/**
 * Class CustomerAttributeElement
 */
class CustomerAddressAttributeElement extends CustomerAttributeElement
{
    public function __construct(
        \Magento\Eav\Setup\EavSetup $eavSetup,
        Attribute $attributeResourceModel,
        AttributeRepositoryInterface $attributeRepository,
        ElementInterface $parent,
        $code
    ) {
        parent::__construct($eavSetup, $attributeResourceModel, $attributeRepository, $parent, $code);
        $this->setEntityTypeId('customer_address');
    }
}
