<?php
/**
 * @author      Benjamin Rosenberger <rosenberger@e-conomix.at>
 * @package BroCode\EntityServices\Model\Schema
 * @copyright Copyright (c) 2020 E-CONOMIX GmbH (https://www.e-conomix.at)
 * @created 23.08.2020
 */

namespace BroCode\EntityServices\Model\Schema;

/**
 * Class AbstractElement
 * @package BroCode\EntityServices\Model\Schema
 */
abstract class AbstractElement
{
    protected $parent;
    /**
     * @var \Magento\Framework\Setup\SchemaSetupInterface
     */
    protected $setup;

    /**
     * AbstractElement constructor.
     * @param $parent
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     */
    public function __construct($parent, \Magento\Framework\Setup\SchemaSetupInterface $setup)
    {
        $this->parent = $parent;
        $this->setup = $setup;
    }

    abstract public function build();
}
