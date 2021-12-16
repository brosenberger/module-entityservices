<?php
/**
 * @author      Benjamin Rosenberger <rosenberger@e-conomix.at>
 * @package BroCode\EntityServices\Model\Schema
 * @copyright Copyright (c) 2021 E-CONOMIX GmbH (https://www.e-conomix.at)
 * @created 18.01.2021
 */

namespace BroCode\EntityServices\Model\Schema;

use BroCode\EntityServices\Api\ElementInterface;

/**
 * Class SalesEntityAttributeElement
 * @package BroCode\EntityServices\Model\Schema
 */
class SalesEntityAttributeElement implements ElementInterface
{
    /**
     * @var ElementInterface
     */
    protected $parent;
    /**
     * @var \Magento\Sales\Setup\SalesSetup
     */
    protected $salesSetup = null;
    /**
     * @var \Magento\Sales\Setup\SalesSetupFactory
     */
    protected $salesSetupFactory;
    /**
     * @var \Magento\Quote\Setup\QuoteSetup
     */
    protected $quoteSetup;
    /**
     * @var \Magento\Quote\Setup\QuoteSetupFactory
     */
    protected $quoteSetupFactory;
    /**
     * @var \Magento\Framework\Setup\ModuleDataSetupInterface
     */
    protected $setup;

    /**
     * SalesEntityAttributeElement constructor.
     * @param $parent
     */
    public function __construct(
        \Magento\Framework\Setup\ModuleDataSetupInterface $setup,
        \Magento\Sales\Setup\SalesSetupFactory $salesSetupFactory,
        \Magento\Quote\Setup\QuoteSetupFactory $quoteSetupFactory,
        $parent
    ) {
        $this->parent = $parent;
        $this->salesSetupFactory = $salesSetupFactory;
        $this->quoteSetupFactory = $quoteSetupFactory;
        $this->setup = $setup;
    }

    public function withQuoteAttribute($attributeCode, array $columnOptions)
    {
        $this->getQuoteSetup()->addAttribute('quote', $attributeCode, $columnOptions);
    }

    public function withQuoteItemAttribute($attributeCode, array $columnOptions)
    {
        $this->getQuoteSetup()->addAttribute('quote_item', $attributeCode, $columnOptions);
    }

    public function withQuoteAddressAttribute($attributeCode, array $columnOptions)
    {
        $this->getQuoteSetup()->addAttribute('quote_address', $attributeCode, $columnOptions);
    }

    public function withQuoteAddressItemAttribute($attributeCode, array $columnOptions)
    {
        $this->getQuoteSetup()->addAttribute('quote_address_item', $attributeCode, $columnOptions);
    }

    public function withOrderAttribute($attributeCode, array $columnOptions)
    {
        return $this->withSalesEntityAttribute('order', $attributeCode, $columnOptions);
    }

    public function withOrderAddressAttribute($attributeCode, array $columnOptions)
    {
        return $this->withSalesEntityAttribute('order_address', $attributeCode, $columnOptions);
    }

    public function withOrderItemAttribute($attributeCode, array $columnOptions)
    {
        return $this->withSalesEntityAttribute('order_item', $attributeCode, $columnOptions);
    }

    public function withInvoiceAttribute($attributeCode, array $columnOptions)
    {
        return $this->withSalesEntityAttribute('invoice', $attributeCode, $columnOptions);
    }

    public function withSalesEntityAttribute($entityType, $attributeCode, array $columnOptions)
    {
        $this->getSalesSetup()->addAttribute($entityType, $attributeCode, $columnOptions);
        return $this;
    }

    public function build()
    {
        return $this->parent;
    }

    /**
     * @return \Magento\Sales\Setup\SalesSetup
     */
    protected function getQuoteSetup() {
        if ($this->quoteSetup == null) {
            $this->quoteSetup = $this->quoteSetupFactory->create(['resourceName' => 'quote_setup', 'setup' => $this->setup]);
        }
        return $this->quoteSetup;
    }
    /**
     * @return \Magento\Sales\Setup\SalesSetup
     */
    protected function getSalesSetup() {
        if ($this->salesSetup == null) {
            $this->salesSetup = $this->salesSetupFactory->create(['resourceName' => 'sales_setup', 'setup' => $this->setup]);
        }
        return $this->salesSetup;
    }
}
