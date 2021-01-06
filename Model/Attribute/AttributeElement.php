<?php
/**
 * @author      Benjamin Rosenberger <rosenberger@e-conomix.at>
 * @copyright Copyright (c) 2021 E-CONOMIX GmbH (https://www.e-conomix.at)
 * @created 04.01.2021
 */

namespace BroCode\EntityServices\Model\Attribute;

use BroCode\EntityServices\Api\ElementInterface;

/**
 * Class AttributeElement
 * Basic class for configuration of an attribute
 */
class AttributeElement implements ElementInterface
{
    /**
     * @var string
     */
    protected $entityTypeId;

    /**
     * @var string
     */
    protected $code;
    /**
     * @var array
     */
    protected $attr = [];

    protected $setId = null;
    protected $groupId = null;
    protected $sortOrder = null;

    /**
     * @var ElementInterface
     */
    protected $parent;
    /**
     * @var \Magento\Eav\Setup\EavSetup
     */
    protected $eavSetup;

    /**
     * AttributeElement constructor.
     * @param ElementInterface $parent
     * @param string $code
     */
    public function __construct(\Magento\Eav\Setup\EavSetup $eavSetup, ElementInterface $parent, $entityTypeId, $code)
    {
        $this->parent = $parent;
        $this->code = $code;
        $this->setEntityTypeId($entityTypeId);
        $this->eavSetup = $eavSetup;
    }

    public function setEntityTypeId($entityTypeId)
    {
        $this->entityTypeId = $entityTypeId;
    }

    public function withAttribute($attribute, $value)
    {
        $this->attr[$attribute] = $value;
        return $this;
    }

    /**
     * @param string $type
     * @return $this
     */
    public function withType($type)
    {
        return $this->withAttribute('type', $type);
    }

    /**
     * @param string $label
     * @return $this
     */
    public function withLabel($label)
    {
        return $this->withAttribute('label', $label);
    }

    public function withDefault($default)
    {
        return $this->withAttribute('default', $default);
    }

    public function withWysiwygEnabled($enabled)
    {
        return $this->withAttribute('wysiwyg_enabled', $enabled == true);
    }

    public function withIsHtmlAllowedOnFront($enabled)
    {
        return $this->withAttribute('is_html_allowed_on_front', $enabled == true);
    }

    /**
     * @param bool $required
     * @return $this
     */
    public function asRequired($required)
    {
        return $this->withAttribute('required', $required == true);
    }

    /**
     * @param bool $visible
     * @return $this
     */
    public function asVisible($visible)
    {
        return $this->withAttribute('visible', $visible == true);
    }

    public function withPosition($position)
    {
        return $this->withAttribute('position', $position);
    }

    public function withScope($scope)
    {
        return $this->withAttribute('global', $scope);
    }

    public function withStoreScope()
    {
        return $this->withScope(\Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE);
    }

    public function withWebsiteScope()
    {
        return $this->withScope(\Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_WEBSITE);
    }

    public function withGlobalScope()
    {
        return $this->withScope(\Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL);
    }

    public function build()
    {
        // add / update attribute
        $this->createOrUpdateAttribute(
            $this->entityTypeId,
            $this->code,
            $this->attr
        );
        $this->addToAttributeSet(
            $this->entityTypeId,
            $this->code,
            $this->setId ?? $this->eavSetup->getDefaultAttributeSetId($this->entityTypeId),
            $this->groupId ?? $this->eavSetup->getDefaultAttributeGroupId($this->entityTypeId),
            $this->sortOrder
        );
        $this->additionalActions();

        return $this->parent;
    }

    protected function createOrUpdateAttribute($entityTypeId, $attributeCode, $attributeData)
    {
        $this->eavSetup->addAttribute($entityTypeId, $attributeCode, $attributeData);
    }

    protected function addToAttributeSet($entityTypeId, $attributeCode, $attrSetId, $attrGroupId, $sortOrder)
    {
        $this->eavSetup->addAttributeToSet(
            $entityTypeId,
            $attrSetId,
            $attrGroupId,
            $this->eavSetup->getAttributeId($entityTypeId, $attributeCode),
            $sortOrder
        );
    }

    protected function additionalActions()
    {
        // nothing to do in here, only subs if they want to
    }
}
