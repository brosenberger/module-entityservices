# Goal of this module should be
* easy way to update your database without knowledge of underlaying array configs
* creating new EAV entity schemas with just a view lines of code
* add EAV attributes for products, customers, categories without knowledge of the differing subtle differences


###DONE:
* create tables with various columns, indizes, foreign keys
* create a set of tables for a EAV entity

###Example:

Usage within a Schema upgrade:
```
class UpgradeSchema implements UpgradeSchemaInterface
   {
       /**
        * @var SchemaBuilder
        */
       private $schemaBuilderFactory;
   
       /**
        * UpgradeSchema constructor.
        * @param SchemaBuilder $schemaBuilderFactory
        */
       public function __construct(SchemaBuilderFactory $schemaBuilderFactory) {
   
           $this->schemaBuilderFactory = $schemaBuilderFactory;
       }
   
       public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
       {
           $setup->startSetup();
           if (version_compare($context->getVersion(), '1.1.0', '<')) {
               $this->schemaBuilderFactory->create($setup)->withTable('entity')
                   ->withIntColumn('entity_id')->asIdentiy()->asUnsigned()->asNullable(false)->asPrimaryKey()->build()
                   ->withVarcharColumn('field_id)->asNullable(false)->build()
                   ->withIndex(['field_id'])->build()
                   ->withForeignKey('field_id, 'other_table', 'other_column')->actionNoAction()->build()
                   ->build();
           }
           $setup->endSetup();
       }
   }
```