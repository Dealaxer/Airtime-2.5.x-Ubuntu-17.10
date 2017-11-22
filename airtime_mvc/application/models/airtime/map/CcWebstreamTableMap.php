<?php



/**
 * This class defines the structure of the 'cc_webstream' table.
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
class CcWebstreamTableMap extends TableMap {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'airtime.map.CcWebstreamTableMap';

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
		$this->setName('cc_webstream');
		$this->setPhpName('CcWebstream');
		$this->setClassname('CcWebstream');
		$this->setPackage('airtime');
		$this->setUseIdGenerator(true);
		$this->setPrimaryKeyMethodInfo('cc_webstream_id_seq');
		// columns
		$this->addPrimaryKey('ID', 'DbId', 'INTEGER', true, null, null);
		$this->addColumn('NAME', 'DbName', 'VARCHAR', true, 255, null);
		$this->addColumn('DESCRIPTION', 'DbDescription', 'VARCHAR', true, 255, null);
		$this->addColumn('URL', 'DbUrl', 'VARCHAR', true, 512, null);
		$this->addColumn('LENGTH', 'DbLength', 'VARCHAR', true, null, '00:00:00');
		$this->addColumn('CREATOR_ID', 'DbCreatorId', 'INTEGER', true, null, null);
		$this->addColumn('MTIME', 'DbMtime', 'TIMESTAMP', true, 6, null);
		$this->addColumn('UTIME', 'DbUtime', 'TIMESTAMP', true, 6, null);
		$this->addColumn('LPTIME', 'DbLPtime', 'TIMESTAMP', false, 6, null);
		$this->addColumn('MIME', 'DbMime', 'VARCHAR', false, 255, null);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
    $this->addRelation('CcSchedule', 'CcSchedule', RelationMap::ONE_TO_MANY, array('id' => 'stream_id', ), 'CASCADE', null);
	} // buildRelations()

} // CcWebstreamTableMap
