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

    public function getAttribute($attribute, $defaultValue = null)
    {
        if (isset($this->attr[$attribute])) {
            return $this->attr[$attribute];
        }
        return $defaultValue;
    }

    public function withAttribute($attribute, $value)
    {
        $this->attr[$attribute] = $value;
        return $this;
    }


    public function inGroup($groupName)
    {
        return $this->withAttribute('group', $groupName);
    }
    public function withFrontendInput($inputType)
    {
        return $this->withAttribute('input', $inputType);
    }
    public function withInputBoolean()
    {
        return $this->withFrontendInput('boolean');
    }
    public function withInputText()
    {
        return $this->withFrontendInput('text');
    }
    public function withInputTextarea()
    {
        return $this->withFrontendInput('textarea');
    }
    public function withInputSelect()
    {
        return $this->withFrontendInput('select');
    }
    public function withInputMultiselect()
    {
        return $this->withFrontendInput('multiselect');
    }
    public function withInputDate()
    {
        return $this->withFrontendInput('date');
    }

    public function withFrontendClass($frontendClass)
    {
        return $this->withAttribute('class', $frontendClass);
    }

    public function withFrontendModel($frontendModel)
    {
        return $this->withAttribute('frontend', $frontendModel);
    }
    public function withBackendModel($backendModel)
    {
        return $this->withAttribute('backend', $backendModel);
    }
    public function withSource($sourceModel)
    {
        return $this->withAttribute('source', $sourceModel);
    }

    /**
     * @param string $type
     * @return $this
     */
    public function withType($type)
    {
        return $this->withAttribute('type', $type);
    }

    public function withTypeStatic()
    {
        return $this->withType('static');
    }

    public function withTypeVarchar()
    {
        return $this->withType('varchar');
    }

    public function withTypeInt()
    {
        return $this->withType('int');
    }

    public function withTypeText()
    {
        return $this->withType('text');
    }

    public function withTypeDatetime()
    {
        return $this->withType('datetime');
    }

    public function withTypeDecimal()
    {
        return $this->withType('decimal');
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

    public function asUserDefined($userDefined)
    {
        return $this->withAttribute('user_defined', $userDefined == true);
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

    public function withSortOrder($sortOrder)
    {
        return $this->withAttribute('sort_order', $sortOrder);
    }

    public function build()
    {
        // add / update attribute
        $this->createOrUpdateAttribute(
            $this->entityTypeId,
            $this->code,
            $this->attr
        );

        $setId = $this->setId ?? $this->eavSetup->getDefaultAttributeSetId($this->entityTypeId);
        $groupId = $this->eavSetup->getAttributeGroup(
            $this->entityTypeId,
            $setId,
            $this->getAttribute(
                'group',
                $this->groupId ?? $this->eavSetup->getDefaultAttributeGroupId($this->entityTypeId)
            ),
            'attribute_group_id'
        );

        $this->addToAttributeSet(
            $this->entityTypeId,
            $this->code,
            $setId,
            $groupId,
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

    // @codingStandardsIgnoreStart empty function
    protected function additionalActions()
    {
        // nothing to do in here, only subs if they want to
    }
    // @codingStandardsIgnoreEnd
}
