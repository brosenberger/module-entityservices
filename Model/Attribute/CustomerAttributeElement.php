<?php
/**
 * @author      Benjamin Rosenberger <rosenberger@e-conomix.at>
 * @copyright Copyright (c) 2021 E-CONOMIX GmbH (https://www.e-conomix.at)
 * @created 04.01.2021
 */

namespace BroCode\EntityServices\Model\Attribute;

use BroCode\EntityServices\Api\ElementInterface;
use Magento\Customer\Model\ResourceModel\Attribute;
use Magento\Eav\Api\AttributeRepositoryInterface;

/**
 * Class CustomerAttributeElement
 *
 * additional methods for customer specific attributes (like formhandling)
 */
class CustomerAttributeElement extends AttributeElement
{
    const DEFAULT_FORMS = ['customer_account_edit', 'adminhtml_customer'];

    /**
     * @var Attribute
     */
    protected $attributeResourceModel;
    /**
     * @var AttributeRepositoryInterface
     */
    protected $attributeRepository;

    protected $forms = [];

    public function __construct(
        \Magento\Eav\Setup\EavSetup $eavSetup,
        Attribute $attributeResourceModel,
        AttributeRepositoryInterface $attributeRepository,
        ElementInterface $parent,
        $code
    ) {
        parent::__construct($eavSetup, $parent, \Magento\Customer\Model\Customer::ENTITY, $code);
        $this->attributeResourceModel = $attributeResourceModel;
        $this->attributeRepository = $attributeRepository;
    }

    public function withDefaults()
    {
        return $this->asSystem(false)
            ->asUserDefined(true)
            ->asVisible(true)
            ->addToForm($this->getDefaultForms());
    }

    protected function getDefaultForms()
    {
        return self::DEFAULT_FORMS;
    }

    /**
     * @param bool $system
     * @return $this
     */
    public function asSystem($system)
    {
        return $this->withAttribute('system', $system == true ? 1 : 0);
    }

    public function asUserDefined($userDefined)
    {
        return $this->withAttribute('user_defined', $userDefined == true);
    }

    public function addToForm($forms)
    {
        if (is_array($forms)) {
            $this->forms = array_merge($this->forms, $forms);
        } else {
            $this->forms[] = $forms;
        }
        return $this;
    }

    public function saveAddToForms($forms)
    {
        $attribute = $this->attributeRepository->get($this->entityTypeId, $this->code);
        $attribute->setData('used_in_forms', $forms);
        $this->attributeResourceModel->save($attribute);
        return $this;
    }

    public function asUsedInGrid($usedInGrid = true)
    {
        return $this->withAttribute('is_used_in_grid', $usedInGrid);
    }

    public function asVisibleInGrid($visibleInGrid = true)
    {
        return $this->withAttribute('is_visible_in_grid', $visibleInGrid);
    }

    public function asFilterableInGrid($filterableInGrid = true)
    {
        return $this->withAttribute('is_filterable_in_grid', $filterableInGrid);
    }

    public function asSearchableInGrid($searchableInGrid = true)
    {
        return $this->withAttribute('is_searchable_in_grid', $searchableInGrid);
    }

    protected function additionalActions()
    {
        parent::additionalActions();
        if (count($this->forms)>0) {
            $this->saveAddToForms($this->forms);
        }
    }
}
