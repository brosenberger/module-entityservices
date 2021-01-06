<?php
/**
 * @author      Benjamin Rosenberger <rosenberger@e-conomix.at>
 * @package BroCode\EntityServices\Model\Attribute
 * @copyright Copyright (c) 2021 E-CONOMIX GmbH (https://www.e-conomix.at)
 * @created 04.01.2021
 */

namespace BroCode\EntityServices\Model\Attribute;

use BroCode\EntityServices\Api\ElementInterface;

/**
 * Class ProductAttributeElement
 */
class ProductAttributeElement extends AttributeElement
{
    const ALLPRODUCTTYPES = ["simple","configurable","virtual","bundle","downloadable","grouped"];

    public function __construct(\Magento\Eav\Setup\EavSetup $eavSetup, ElementInterface $parent, $code)
    {
        parent::__construct($eavSetup, $parent, \Magento\Catalog\Model\Product::ENTITY, $code);
    }

    public function asSearchable($searchable)
    {
        return $this->withAttribute('searchable', $searchable == true);
    }

    public function asFilterable($filterable)
    {
        return $this->withAttribute('filterable', $filterable == true);
    }

    public function asComparable($comparable)
    {
        return $this->withAttribute('comparable', $comparable == true);
    }

    public function asVisibleOnFron($visible)
    {
        return $this->withAttribute('visible_on_fron', $visible == true);
    }

    public function asUsedInProductListing($usedInProductListing)
    {
        return $this->withAttribute('used_in_prodict_listing', $usedInProductListing == true);
    }

    public function asUnique($unique)
    {
        return $this->withAttribute('unique', $unique == true);
    }

    /**
     * @param string|array $applyTo
     * @return ProductAttributeElement
     */
    public function withApplyTo($applyTo)
    {
        return $this->withAttribute('apply_to', implode(',', is_array($applyTo) ? $applyTo : [$applyTo]));
    }

    public function withApplyToAll()
    {
        return $this->withApplyTo(self::ALLPRODUCTTYPES);
    }
}
