<?php



/**
 * This class defines the structure of the 'cc_show_days' table.
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
class CcShowDaysTableMap extends TableMap {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'airtime.map.CcShowDaysTableMap';

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
		$this->setName('cc_show_days');
		$this->setPhpName('CcShowDays');
		$this->setClassname('CcShowDays');
		$this->setPackage('airtime');
		$this->setUseIdGenerator(true);
		$this->setPrimaryKeyMethodInfo('cc_show_days_id_seq');
		// columns
		$this->addPrimaryKey('ID', 'DbId', 'INTEGER', true, null, null);
		$this->addColumn('FIRST_SHOW', 'DbFirstShow', 'DATE', true, null, null);
		$this->addColumn('LAST_SHOW', 'DbLastShow', 'DATE', false, null, null);
		$this->addColumn('START_TIME', 'DbStartTime', 'TIME', true, null, null);
		$this->addColumn('TIMEZONE', 'DbTimezone', 'VARCHAR', true, 255, null);
		$this->addColumn('DURATION', 'DbDuration', 'VARCHAR', true, 255, null);
		$this->addColumn('DAY', 'DbDay', 'TINYINT', false, null, null);
		$this->addColumn('REPEAT_TYPE', 'DbRepeatType', 'TINYINT', true, null, null);
		$this->addColumn('NEXT_POP_DATE', 'DbNextPopDate', 'DATE', false, null, null);
		$this->addForeignKey('SHOW_ID', 'DbShowId', 'INTEGER', 'cc_show', 'ID', true, null, null);
		$this->addColumn('RECORD', 'DbRecord', 'TINYINT', false, null, 0);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
    $this->addRelation('CcShow', 'CcShow', RelationMap::MANY_TO_ONE, array('show_id' => 'id', ), 'CASCADE', null);
	} // buildRelations()

} // CcShowDaysTableMap
