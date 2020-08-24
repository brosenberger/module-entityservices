<?php
/**
 * @author      Benjamin Rosenberger <rosenberger@e-conomix.at>
 * @package BroCode\EntityServices\Model\Schema
 * @copyright Copyright (c) 2020 E-CONOMIX GmbH (https://www.e-conomix.at)
 * @created 23.08.2020
 */

namespace BroCode\EntityServices\Model\Schema;

use BroCode\EntityServices\Api\ElementInterface;

/**
 * Class AbstractElement
 * @package BroCode\EntityServices\Model\Schema
 */
abstract class AbstractElement implements ElementInterface
{
    /**
     * @var ElementInterface
     */
    protected $parent;
    /**
     * @var \Magento\Framework\Setup\SchemaSetupInterface
     */
    protected $setup;

    /**
     * AbstractElement constructor.
     * @param ElementInterface $parent
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     */
    public function __construct($parent, \Magento\Framework\Setup\SchemaSetupInterface $setup)
    {
        $this->parent = $parent;
        $this->setup = $setup;
    }
}
