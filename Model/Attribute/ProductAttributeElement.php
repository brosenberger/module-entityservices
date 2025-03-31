<?php
/**
 * @author      Benjamin Rosenberger <rosenberger@e-conomix.at>
 * @copyright Copyright (c) 2021 E-CONOMIX GmbH (https://www.e-conomix.at)
 * @created 04.01.2021
 */

namespace BroCode\EntityServices\Model\Attribute;

use BroCode\EntityServices\Api\ElementInterface;

/**
 * Class ProductAttributeElement
 * Specialized methods for product attributes (product type specification,...)
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

    /**
     * @depecated use correctly spelled method asVisibleOnFront
     */
    public function asVisibleOnFron($visible)
    {
        return $this->asVisibleOnFront($visible);
    }
    public function asVisibleOnFront($visible)
    {
        return $this->withAttribute('visible_on_front', $visible == true);
    }

    public function asUsedInProductListing($usedInProductListing)
    {
        return $this->withAttribute('used_in_product_listing', $usedInProductListing == true);
    }

    public function asUsedForPromoRules($usedForPromoRules)
    {
        return $this->withAttribute('used_for_promo_rules', $usedForPromoRules == true);
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

    public function withInputMediaImage()
    {
        return $this->withFrontendInput('media_image');
    }

    public function withImageFrontendModel()
    {
        return $this->withFrontendModel('Magento\Catalog\Model\Product\Attribute\Frontend\Image');
    }

    /**
     * Used for a product image role
     * @return ProductAttributeElement
     */
    public function asMediaImage() {
        return $this->withTypeVarchar()->withInputMediaImage()->withImageFrontendModel();
    }

    public function inGroupGeneral()
    {
        return $this->inGroup('General');
    }
    public function inGroupPrices()
    {
        return $this->inGroup('Prices');
    }
    public function inGroupMetaInformation()
    {
        return $this->inGroup('Meta Information');
    }
    public function inGroupImages()
    {
        return $this->inGroup('Images');
    }
    public function inGroupDesign()
    {
        return $this->inGroup('Design');
    }
}
