<?php



/**
 * This class defines the structure of the 'cc_stamp' table.
 *
 *
 *
 * This map class is used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 * @package    propel.generator.airtime.map
 */
class CcStampTableMap extends TableMap {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'airtime.map.CcStampTableMap';

	/**
	 * Initialize the table attributes, columns and validators
	 * Relations are not initialized by this method since they are lazy loaded
	 *
	 * @return     void
	 * @throws     PropelException
	 */
	public function initialize()
	{
	  // attributes
		$this->setName('cc_stamp');
		$this->setPhpName('CcStamp');
		$this->setClassname('CcStamp');
		$this->setPackage('airtime');
		$this->setUseIdGenerator(true);
		$this->setPrimaryKeyMethodInfo('cc_stamp_id_seq');
		// columns
		$this->addPrimaryKey('ID', 'DbId', 'INTEGER', true, null, null);
		$this->addForeignKey('SHOW_ID', 'DbShowId', 'INTEGER', 'cc_show', 'ID', true, null, null);
		$this->addForeignKey('INSTANCE_ID', 'DbInstanceId', 'INTEGER', 'cc_show_instances', 'ID', false, null, null);
		$this->addColumn('LINKED', 'DbLinked', 'BOOLEAN', true, null, null);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
    $this->addRelation('CcShow', 'CcShow', RelationMap::MANY_TO_ONE, array('show_id' => 'id', ), 'CASCADE', null);
    $this->addRelation('CcShowInstances', 'CcShowInstances', RelationMap::MANY_TO_ONE, array('instance_id' => 'id', ), 'CASCADE', null);
    $this->addRelation('CcStampContents', 'CcStampContents', RelationMap::ONE_TO_MANY, array('id' => 'stamp_id', ), 'CASCADE', null);
	} // buildRelations()

} // CcStampTableMap
