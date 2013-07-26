<?php
require_once "ArrayDataSet.php";
require_once "Reporter.php";

abstract class PHPUnit_Extensions_Database_TestCase_CreateTable extends PHPUnit_Extensions_Database_TestCase
{
	/**
	 *	Only instantiate PDO once for test clean-up/fixture load.
	 *
	 *	@access private
	 */
	static private $pdo = null;
	
	
	/**
	 *	Only instantiate PHPUnit_Extensions_Database_DB_IDatabaseConnection once per test.
	 *
	 *	@access private
	 */
	private $conn = null;
	
	
	/**
	 *	Reporter object. Only need one for the entire test run.
	 *
	 *	@access private
	 */
	static private $reporter = null;
	
	
	/**
	 *	List of possible mark adjustments for errors (negative) or bonuses (positive).
	 *
	 *	@access protected
	 */
	protected $markAdjustments = array(
		'missingTable'			=>	-5,
		
		'missingColumn'			=>	-1,
		'incorrectPK'			=>	-1,
		'incorrectFK'			=>	-1,
		'misspelledIdentifier'	=>	-1,
		'incorrectDataType'		=>	-1,
		'missingCheck'			=>	-1,
		
		'delimitedIdentifier'	=>	-0.5,
		'unnamedConstraint'		=>	-0.5,
		'incorrectLength'		=>	-0.5,
		'incorrectDefault'		=>	-0.5,
		'incorrectCheck'		=>	-0.5,
		'incorrectNullability'	=>	-0.5,
	);
	
	
	/**
	 *	Convert the input text into a form that is acceptable to SQL. Text values are wrapped in '', and any embedded ' are converted to ''. "&" is also converted to "'||chr(38)||'" (mainly for Oracle).
	 */
	protected function sqlifyValue( $srcValue, $srcType )
	{
		$sqlifiedValue = $srcValue;
		if ( $srcType === 'TEXT' )
		{
			$sqlifiedValue = str_replace( "'", "''", $sqlifiedValue );
			$sqlifiedValue = str_replace( '&', "' || chr(38) || '", $sqlifiedValue );
		}
		if ( ( $srcType === 'TEXT' ) || ( $srcType === 'DATE' ) )
		{
			$sqlifiedValue = "'" . $sqlifiedValue . "'";
		}
		
		return $sqlifiedValue;
	}
	
	
	/**#@+
	 *	Return table name, list of columns, etc.
	 *
	 *	These are a hack. I would have preferred to have them as class member variables, but this doesn't work with parameterised tests based on data providers, because the data providers are executed before the member variables are initialised (even before static member initialisation!!). This means that the member variables would be empty at the time the provider runs, and thus nothing would happen.
	 *
	 *	The workaround is for subclasses to implement the following methods. Each method should return the appropriate value to the caller (e.g., an array of strings for the column list).
	 *
	 *	@abstract
	 *	@access protected
	 */
	 
	 
	/**
	 *	Return the SQL table name.
	 *
	 *	@return string
	 */
	abstract protected function getTableName();
	
	
	/**
	 *	Return a list of the table's column names.
	 *
	 *	@return array( string+ )
	 */
	abstract protected function getColumnList();
	
	
	/**
	 *	Return a list of the table's primary key column names.
	 *
	 *	@return array( string+ )
	 */
	abstract protected function getPKColumnList();
	
	
	/**
	 *	Return a list of the table's foreign key column names.
	 *
	 *	This should return a list of column names for each FK, indexed by the name of the referenced table.
	 *
	 *	@return array( string => array( string+ ) )
	 */
	abstract protected function getFKColumnList();
	
	/**#@-*/
	
	
	/**
	 *	Return the list of primary key column names as an array dataset.
	 *
	 *	The argument specifies the name of the table in the dataset.
	 *
	 *	@return SchemaTesting_DbUnit_ArrayDataSet
	 */
	protected function getPKColumnListAsDataSet( $datasetTableName )
	{
		$theList = array();
		foreach ( $this->getPKColumnList() as $columnName )
		{
			array_push( $theList, array( 'COLUMN_NAME' => $columnName ) );
		}
		
		return new SchemaTesting_DbUnit_ArrayDataSet( array( $datasetTableName => $theList ) );
	}
	
	
	/**
	 *	Return the list of column names for the foreign key on the current table that references the specified table.
	 *
	 *	@return array( string+ )
	 */
	protected function getFKColumnListForTable( $referencedTable )
	{
		$theList = array();
		$allFKColumns = $this->getFKColumnList();
		
		return $allFKColumns[$referencedTable];
	}
	
	
	/**
	 *	Return the list of column names for a given foreign key as an array data set.
	 *
	 *	The second argument specifies the name of the table in the dataset.
	 *
	 *	@return SchemaTesting_DbUnit_ArrayDataSet
	 */
	protected function getFKColumnListForTableAsDataSet( $referencedTable, $datasetTableName )
	{
		$theList = array();
		$fkColumns = $this->getFKColumnListForTable( $referencedTable );
		
		foreach ( $fkColumns as $columnName  )
		{
			array_push( $theList, array( 'COLUMN_NAME' => $columnName ) );
		}
		
		return new SchemaTesting_DbUnit_ArrayDataSet( array( $datasetTableName => $theList ) );
	}
	
	
	/**
	 *	Return a test INSERT statement, using standard test values except for those specified in the substitutions list. If you want to test NULL or DEFAULT, just substitute the standard test value with the 'NULL' and 'DEFAULT' keywords, respectively.
	 *
	 *	@return string
	 */
	protected function constructInsert( $substitutions )
	{
		$columnValues = array();
		foreach ( $this->getColumnList() as $name => $details )
		{
			if ( array_key_exists( $name, $substitutions ) )
			{
				$columnValues[] = $this->sqlifyValue( $substitutions[$name], $details['generic_type'] );
				unset( $substitutions[$name] );
			}
			else
			{
				$columnValues[] = $this->sqlifyValue( $details['test_value'], $details['generic_type'] );
			}
		}
		return sprintf(
			"INSERT INTO %s ( %s ) VALUES ( %s )",
			$this->getTableName(),
			implode( ', ', array_keys( $this->getColumnList() ) ),
			implode( ', ', $columnValues )
		);
	}
	
	
	/**
	 *	Return a test INSERT statement, using standard test values.
	 *
	 *	This just works by calling constructInsert with an empty substitutions list.
	 *
	 *	@return string
	 */
	protected function getStandardTestInsert()
	{
		return constructInsert( array() );
	}
	
	
	/**
	 *	Return database connection.
	 *
	 *	@access protected
	 *	@return PHPUnit_Extensions_Database_DB_IDatabaseConnection
	 *	@todo Parameterise the connection details.
	 */
	protected function getConnection()
	{
		if ( $this->conn === null )
		{
			if ( self::$pdo == null )
			{
				self::$pdo = new PDO( "oci:dbname=" . ORACLE_SERVICE_ID, ORACLE_USERNAME, ORACLE_PASSWORD );
			}
			$this->conn = $this->createDefaultDBConnection( self::$pdo, ORACLE_USERNAME );
		}

		return $this->conn;
	}
	
	
	/**
	 *	Return the reporter object.
	 *
	 *	@access public
	 *	@return Reporter
	 */
	static public function getReporter()
	{
		return self::$reporter;
	}
	
	
	/**
	 *	Set the reporter object.
	 *
	 *	@access public
	 *	@return void
	 */
	static public function setReporter( $newReporter )
	{
		self::$reporter = $newReporter;
	}
	
	
	/**
	 *	Return the fixture setup operation.
	 *
	 *	We can't use the standard fixture setup operation with Oracle, because TRUNCATE doesn't work on tables that are referenced by foreign keys, even if the table is empty! We use the DELETE_ALL action operation instead.
	 *
	 *	@access protected
	 *	@return PHPUnit_Extensions_Database_Operation_DatabaseOperation
	 */
	protected function getSetUpOperation()
	{
		return new PHPUnit_Extensions_Database_Operation_Composite(
			array(
				PHPUnit_Extensions_Database_Operation_Factory::DELETE_ALL(),
				PHPUnit_Extensions_Database_Operation_Factory::INSERT()
			)
		);
	}
	
	
	/**
	 *	Return the fixture teardown operation.
	 *
	 *	We can't use the standard fixture teardown operation with Oracle, because TRUNCATE doesn't work on tables that are referenced by foreign keys, even if the table is empty! We use the DELETE_ALL action operation instead.
	 *
	 *	@access protected
	 *	@return PHPUnit_Extensions_Database_Operation_DatabaseOperation
	 */
	protected function getTearDownOperation()
	{
		return new PHPUnit_Extensions_Database_Operation_Composite(
			array( PHPUnit_Extensions_Database_Operation_Factory::DELETE_ALL() )
		);
	}
	
	
	/**
	 *	Data provider to return a list of all column names.
	 *
	 *	If your test needs to iterate through all of the table's columns, then use this method as the data provider. Each column name is presented to the consumer in turn.
	 *
	 *	@access public
	 *	@return array( array( string )* )
	 */
	public function provideColumnNames()
	{
		$theList = array();
		foreach ( array_keys( $this->getColumnList() ) as $columnName )
		{
			array_push( $theList, array( $columnName ) );
		}
		
		return $theList;
	}
	
	
	/**
	 *	Data provider to return a list of all columns and their data types.
	 *
	 *	If your test needs to iterate through all column data types of a table, then use this method as the data provider. Each column name plus a list of possible data types is presented to the consumer in turn.
	 *
	 *	@access public
	 *	@return array( array( string, array( string+ ) )* )
	 */
	public function provideColumnTypes()
	{
		$theList = array();
		foreach ( $this->getColumnList() as $columnName => $columnDetails )
		{
			array_push( $theList, array( $columnName, $columnDetails['sql_type'] ) );
		}
		
		return $theList;
	}
	
	
	/**
	 *	Data provider to return a list of all columns and their length-related information.
	 *
	 *	If your test needs to iterate through all column lengths of a table, then use this method as the data provider. Each column name plus its generic data type, minimum length, maximum length (for a numeric column, this represents the precision) and number of decimal places (technically, the scale) are presented to the consumer in turn. Columns with no specified length (e.g., dates, BLOBs, CLOBs) should not have their min_length and max_length values set (although CLOBs are a slightly tricky case where the same effect could be achieved using a largish VARCHAR2 --- this is handled as a special case in assertColumnLength() below).
	 *
	 *	@access public
	 *	@return array( array( string, string, int, int, int )* )
	 */
	public function provideColumnLengths()
	{
		$theList = array();
		foreach ( $this->getColumnList() as $columnName => $columnDetails )
		{
			$minLength = ( array_key_exists( 'min_length', $columnDetails ) ) ? $columnDetails['min_length'] : 0;
			$maxLength = ( array_key_exists( 'max_length', $columnDetails ) ) ? $columnDetails['max_length'] : 0;
			$numDecimals = ( array_key_exists( 'decimals', $columnDetails ) ) ? $columnDetails['decimals'] : 0;
			
			// If min_length and max_length are missing, then it has no length at all (e.g., DATE, BLOB, CLOB).
			if ( ( $minLength > 0 ) || ( $maxLength > 0 ) )
			{
				array_push( $theList, array( $columnName, $columnDetails['generic_type'], $minLength, $maxLength, $numDecimals ) );
			}
		}
		
		// If there are none (pretty unlikely), push a marker onto the stack so that we can skip the test.
		if ( count( $theList ) == 0 )
		{
			array_push( $theList, array( '___NO_DATA___', 'NULL', 0, 0, 0 ) );
		}
		
		return $theList;
	}
	
	
	/**
	 *	Data provider to return a list of all columns and their nullabilities.
	 *
	 *	If your test needs to iterate through all column nullabilities of a table, then use this method as the data provider. Each column name plus the appropriate nullability value is presented to the consumer in turn.
	 *
	 *	@access public
	 *	@return array( array( string, string )* )
	 */
	public function provideColumnNullabilities()
	{
		$theList = array();
		foreach ( $this->getColumnList() as $columnName => $columnDetails )
		{
			$isNullable = $columnDetails['nullable'] ? 'Y' : 'N';
			array_push( $theList, array( $columnName, $isNullable ) );
		}
		
		return $theList;
	}
	
	
	/**
	 *	Data provider to return a list of all columns and their legal (enumerated) values.
	 *
	 *	If your test needs to iterate through all legal values of the columns of a table, then use this method as the data provider. Each column name plus a valid legal value is presented to the consumer in turn. (Only for those columns that have them.)
	 *
	 *	@access public
	 *	@return array( array( string, array( string+ ) )* )
	 */
	public function provideColumnLegalValues()
	{
		$theList = array();
		foreach ( $this->getColumnList() as $columnName => $columnDetails )
		{
			if ( array_key_exists( 'legal_values', $columnDetails ) )
			{
				foreach ( $columnDetails['legal_values'] as $legalValue )
				{
					array_push( $theList, array( $columnName, $legalValue ) );
				}
			}
		}
		
		// If there are none, push a marker onto the stack so that we can skip the test.
		if ( count( $theList ) == 0 )
		{
			array_push( $theList, array( '___NO_DATA___', array() ) );
		}
		
		return $theList;
	}
	
	
	/**
	 *	Data provider to return a list of all text columns and some illegal (enumerated) values.
	 *
	 *	If your test needs to iterate through some illegal values of the columns of a table, then use this method as the data provider. Each column name plus a known illegal value is presented to the consumer in turn. (Only for those columns that have them.)
	 *
	 *	@access public
	 *	@return array( array( string, array( string+ ) )* )
	 */
	public function provideColumnIllegalValues()
	{
		$theList = array();
		foreach ( $this->getColumnList() as $columnName => $columnDetails )
		{
			if ( array_key_exists( 'illegal_values', $columnDetails ) )
			{
				foreach ( $columnDetails['illegal_values'] as $illegalValue )
				{
					array_push( $theList, array( $columnName, $illegalValue ) );
				}
			}
		}
		
		// If there are none, push a marker onto the stack so that we can skip the test.
		if ( count( $theList ) == 0 )
		{
			array_push( $theList, array( '___NO_DATA___', array() ) );
		}
		
		return $theList;
	}
	
	
	/**
	 *	Data provider to return a list of all columns and their underflow values.
	 *
	 *	If your test needs to iterate through all underflow values of the columns of a table, then use this method as the data provider. Each column name plus a valid underflow value is presented to the consumer in turn. (Only for those columns that have them.)
	 *
	 *	@access public
	 *	@return array( array( string, array( string+ ) )* )
	 */
	public function provideColumnUnderflowValues()
	{
		$theList = array();
		foreach ( $this->getColumnList() as $columnName => $columnDetails )
		{
			if ( array_key_exists( 'underflow', $columnDetails ) )
			{
				array_push( $theList, array( $columnName, $columnDetails['underflow'] ) );
			}
		}
		
		// If there are none, push a marker onto the stack so that we can skip the test.
		if ( count( $theList ) == 0 )
		{
			array_push( $theList, array( '___NO_DATA___', 0 ) );
		}
		
		return $theList;
	}
	
	
	/**
	 *	Data provider to return a list of all columns and their overflow values.
	 *
	 *	If your test needs to iterate through all overflow values of the columns of a table, then use this method as the data provider. Each column name plus a valid overflow value is presented to the consumer in turn. (Only for those columns that have them.)
	 *
	 *	@access public
	 *	@return array( array( string, array( string+ ) )* )
	 */
	public function provideColumnOverflowValues()
	{
		$theList = array();
		foreach ( $this->getColumnList() as $columnName => $columnDetails )
		{
			if ( array_key_exists( 'overflow', $columnDetails ) )
			{
				array_push( $theList, array( $columnName, $columnDetails['overflow'] ) );
			}
		}
		
		// If there are none, push a marker onto the stack so that we can skip the test.
		if ( count( $theList ) == 0 )
		{
			array_push( $theList, array( '___NO_DATA___', 0 ) );
		}
		
		return $theList;
	}
	
	
	/**
	 *	Data provider to return a list of /actual/ constraint names and types for the current table.
	 *
	 *	If your test needs to iterate through all of the /actual/ constraint names and types of the current table, then use this method as the data provider. Each constraint name and type is presented to the consumer in turn.
	 *
	 *	@access public
	 *	@return array( array( string, string )* )
	 */
	public function provideConstraintNames()
	{
		$theList = array();
		
		// We need to filter on Search_Condition so that we can ignore NOT NULL
		// constraints. However, Search_Condition is a LONG, so we can't query it
		// directly. Instead, we have to create a temporary table, converting the
		// LONG into a CLOB, then query that. Blech.
		//
		// TODO: better exception handling and cleanup around the temporary table.
		// A proper temporary table would be nice, but Oracle's temporary tables at
		// best only truncate themselves at the end of a transaction, rather than go
		// away completely like PostgreSQL temporary tables can. :(
		$createString = sprintf(
			"CREATE TABLE Temp_Constraints AS
			   SELECT Constraint_Name, Constraint_Type,
			          TO_LOB( Search_Condition ) AS Search_Condition
			   FROM User_Constraints
			   WHERE ( Table_Name = '%s' )",
			strtoupper( $this->getTableName() )
		);
		
		$stmt = $this->getConnection()->getConnection()->prepare( $createString );
		
		if ( $stmt->execute() )
		{
			// We also need an NVL on Search_Condition, as some constraint types (notably
			// 'P' and 'R') have a NULL Search_Condition. This couldn't be done in the
			// temporary table, presumably because of the LONG -> LOB conversion.
			$queryString = sprintf(
				"SELECT Constraint_Name, Constraint_Type
				 FROM Temp_Constraints
				 WHERE ( NVL( Search_Condition, 'N/A' ) NOT LIKE '%%IS NOT NULL' )",
				strtoupper( $this->getTableName() )
			);
			
			$actual = $this->getConnection()->createQueryTable( "constraints", $queryString );
			
			for ( $row = 0; $row < $actual->getRowCount(); $row++ )
			{
				array_push( $theList, array( $actual->getValue( $row, 'CONSTRAINT_NAME' ), $actual->getValue( $row, 'CONSTRAINT_TYPE' ) ) );
			}
			
			$stmt = $this->getConnection()->getConnection()->prepare( 'DROP TABLE Temp_Constraints' );
			$stmt->execute();
		}
		
		// If there are none, push a marker onto the stack so that we can skip the test.
		if ( count( $theList ) == 0 )
		{
			array_push( $theList, array( '___NO_DATA___', 0 ) );
		}
		
		return $theList;
	}
	
	
	/**
	 *	Data provider to return a list of column names for the primary key.
	 *
	 *	If your test needs to iterate through all of the columns of the table's primary key, then use this method as the data provider. Each column name is presented to the consumer in turn.
	 *
	 *	@access public
	 *	@return array( array( string )* )
	 */
	public function providePKColumnList()
	{
		$theList = array();
		foreach ( $this->getPKColumnList() as $columnName )
		{
			array_push( $theList, array( $columnName ) );
		}
		
		return $theList;
	}
	
	
	/**
	 *	Data provider to return a list of referenced tables for each foreign key (if any).
	 *
	 *	If your test needs to iterate through all of the referenced tables of the table's foreign keys, then use this method as the data provider. Each referenced table name is presented to the consumer in turn.
	 *
	 *	@access public
	 *	@return array( array( string )* )
	 */
	public function provideFKReferencedTables()
	{
		$theList = array();
		foreach ( $this->getFKColumnList() as $tableName => $columnList )
		{
			array_push( $theList, array( $tableName ) );
		}
		
		return $theList;
	}
	
	
// 	/**
// 	 *	Data provider to return a list of referenced table and columns for each foreign key.
// 	 *
// 	 *	If your test needs to iterate through all of the referenced tables and columns of the table's foreign keys, then use this method as the data provider. Each referenced table name plus a list of the referencing columns is presented to the consumer in turn.
// 	 *
// 	 *	@access public
// 	 *	@return array( array( string, array( string+ ) )* )
// 	 */
// 	public function provideFKDetails()
// 	{
// 		$theList = array();
// 		foreach ( $this->getFKColumnList() as $tableName => $columnList )
// 		{
// 			array_push( $theList, array( $tableName, $columnList ) );
// 		}
// 		
// 		return $theList;
// 	}
	
	
	/**
	 *	Assert that the table exists.
	 *
	 *	This queries Oracle's User_Tables data dictionary view for a table matching the current name.
	 *
	 *	@access protected
	 *	@return void
	 */
	protected function assertTableExists()
	{
// 		echo "\n[[ Testing whether " . ucfirst( strtolower( $this->getTableName() ) ) . " table exists ]]\n";
		self::$reporter->report( Reporter::STATUS_TEST, "[[ %s ]] ", array( ucfirst( strtolower( $this->getTableName() ) ) ) );
		
		$queryString = sprintf(
			"SELECT Table_Name
			 FROM User_Tables
			 WHERE ( Table_Name = '%s' )",
			strtoupper( $this->getTableName() )
		);
//	 	echo "$queryString\n";
		
		$errorString = sprintf(
			"couldn't find the %s table [%+1.1f] --- check for misspelled [%+1.1f] or delimited [%+1.1f] identifiers",
			ucfirst( strtolower( $this->getTableName() ) ),
			$this->markAdjustments['missingTable'],
			$this->markAdjustments['misspelledIdentifier'],
			$this->markAdjustments['delimitedIdentifier']
		);
		
		$actual = $this->getConnection()->createQueryTable( "user_tables", $queryString );
		$this->assertEquals( 1, $actual->getRowCount(), $errorString );
	}
	
	
	/**
	 *	Assert that the table has a particular column.
	 *
	 *	This queries Oracle's User_Tab_Cols data dictionary view for a column with the specified name in the current table. Tests that use this should use provideColumnNames() as their data provider.
	 *
	 *	@access protected
	 *	@return void
	 */
	protected function assertColumnExists( $columnName )
	{
// 		echo "\n[[ Testing whether " . ucfirst( strtolower( $this->getTableName() ) ) . '.' . ucfirst( strtolower( $columnName ) ) . " exists ]]\n";
		self::$reporter->report( Reporter::STATUS_TEST, "[[ %s.%s ]] ",
			array( ucfirst( strtolower( $this->getTableName() ) ), ucfirst( strtolower( $columnName ) ) ) );
		
		$queryString = sprintf(
			"SELECT Column_Name
			 FROM User_Tab_Cols
			 WHERE ( Table_Name = '%s' ) AND ( Column_Name = '%s' )",
			strtoupper( $this->getTableName() ),
			strtoupper( $columnName )
		);
//	 	echo "$queryString\n";
		
		$actual = $this->getConnection()->createQueryTable( $this->getTableName() . '_' . $columnName, $queryString );
		
		$errorString = sprintf(
			"couldn't find the %s.%s column --- check for misspelled [%+1.1f] or delimited [%+1.1f] identifiers",
			ucfirst( strtolower( $this->getTableName() ) ),
			ucfirst( strtolower( $columnName ) ),
			$this->markAdjustments['misspelledIdentifier'],
			$this->markAdjustments['delimitedIdentifier']
		);
						
		$this->assertEquals( 1, $actual->getRowCount(), $errorString );
	}
	
	
	/**
	 *	Assert that a column has a particular data type.
	 *
	 *	This queries Oracle's User_Tab_Cols data dictionary view and compares the data type for the specified column of the current table with the expected column name. Tests that use this should use provideColumnTypes as their data provider.
	 *
	 *	@access protected
	 *	@return void
	 */
	protected function assertColumnDataType( $columnName, $columnTypeList )
	{
// 		echo "\n[[ Testing whether " . ucfirst( strtolower( $this->getTableName() ) ) . '.' . ucfirst( strtolower( $columnName ) ) . " data type is " . implode( ' | ', $columnTypeList ) . " ]]\n";
// 		self::$reporter->report( Reporter::STATUS_TEST, "[[ %s.%s: %s ]] ",
// 			array( ucfirst( strtolower( $this->getTableName() ) ), ucfirst( strtolower( $columnName ) ), implode( ' | ', $columnTypeList ) ) );
		self::$reporter->report( Reporter::STATUS_TEST, "[[ %s.%s data type ]] ",
			array( ucfirst( strtolower( $this->getTableName() ) ), ucfirst( strtolower( $columnName ) ) ) );
		
		$queryString = sprintf(
			"SELECT Data_Type
			 FROM User_Tab_Cols
			 WHERE ( Table_Name = '%s' ) AND ( Column_Name = '%s' )",
			strtoupper( $this->getTableName() ),
			strtoupper( $columnName )
		);
//	 	echo "$queryString\n";
		
		$actual = $this->getConnection()->createQueryTable( $this->getTableName() . '_' . $columnName, $queryString );
		
// 		$errorString = sprintf(
// 			'column %s.%s has unexpected data type %s [%+1.1f]',
// 			ucfirst( strtolower( $this->getTableName() ) ),
// 			ucfirst( strtolower( $columnName ) ),
// 			$actual->getValue( 0, 'DATA_TYPE' ),
// 			$this->markAdjustments['incorrectDataType']
// 		);
		$errorString = sprintf(
			'column %s.%s has unexpected data type %s; check the specification again or consult with the teaching staff',
			ucfirst( strtolower( $this->getTableName() ) ),
			ucfirst( strtolower( $columnName ) ),
			$actual->getValue( 0, 'DATA_TYPE' ),
			$this->markAdjustments['incorrectDataType']
		);
						
		$this->assertContains( $actual->getValue( 0, 'DATA_TYPE' ), $columnTypeList, $errorString );
	}
	
	
	/**
	 *	Assert that a column has a particular length range.
	 *
	 *	This queries Oracle's User_Tab_Cols data dictionary view and compares the length for the specified column of the current table with the expected length(s). Tests that use this should use provideColumnLengths as their data provider.
	 *
	 *	@access protected
	 *	@return void
	 */
	protected function assertColumnLength( $columnName, $columnType, $minLength, $maxLength, $numDecimals )
	{
		// This can only happen if all of the columns are things like DATE, BLOB or CLOB.
		// This is pretty unlikely in practice, but you never know...
		if ( $columnName == '___NO_DATA___' )
		{
			$this->markTestSkipped( 'no columns with enumerated legal values' );
		}
	
// 		echo "\n[[ Testing whether " . ucfirst( strtolower( $this->getTableName() ) ) . '.' . ucfirst( strtolower( $columnName ) ) . " length is ";
		$lengthSpec = '';
// 		if ( $maxLength == 0 )
// 		{
// 			$lengthSpec .= "≥ " . $minLength;
// 		}
// 		elseif ( $minLength == 0 )
// 		{
// 			$lengthSpec .= "≤ " . $maxLength;
// 		}
// 		elseif ( $minLength != $maxLength )
// 		{
// 			$lengthSpec .= $minLength . "–" . $maxLength;
// 		}
// 		else
// 		{
// 			$lengthSpec .= $maxLength;
// 		}
// 		
// 		if ( $columnType === 'NUMBER' )
// 		{
// 			if ( $numDecimals > 0 ) // technically if could also be < 0, but this is uncommon
// 			{
// 				$lengthSpec .= " (including " . $numDecimals . " decimal places)";
// 			}
// 		}
		
		self::$reporter->report( Reporter::STATUS_TEST, "[[ %s.%s length %s ]] ",
			array( ucfirst( strtolower( $this->getTableName() ) ), ucfirst( strtolower( $columnName ) ), $lengthSpec ) );
		
		if ( $columnType === 'NUMBER' )
		{
			$queryString = sprintf(
				"SELECT Data_Type, Data_Precision, Data_Scale
				 FROM User_Tab_Cols
				 WHERE ( Table_Name = '%s' ) AND ( Column_Name = '%s' )",
				strtoupper( $this->getTableName() ),
				strtoupper( $columnName )
			);
		}
		else
		{
			// We need to include the data type for text columns so that we can ignore CLOBs.
			$queryString = sprintf(
				"SELECT Data_Type, Char_Length
				 FROM User_Tab_Cols
				 WHERE ( Table_Name = '%s' ) AND ( Column_Name = '%s' )",
				strtoupper( $this->getTableName() ),
				strtoupper( $columnName )
			);
		}
		
		$actual = $this->getConnection()->createQueryTable( $this->getTableName() . '_' . $columnName, $queryString );
		
		if ( $columnType === 'NUMBER' )
		{
// 			$errorString = sprintf(
// 				'column %s.%s has incorrect length %d, %d [%+1.1f]',
// 				ucfirst( strtolower( $this->getTableName() ) ),
// 				ucfirst( strtolower( $columnName ) ),
// 				$actual->getValue( 0, 'DATA_PRECISION' ),
// 				$actual->getValue( 0, 'DATA_SCALE' ),
// 				$this->markAdjustments['incorrectLength']
// 			);
			$errorString = sprintf(
				'column %s.%s has unexpected length; check the specification again or consult with the teaching staff',
				ucfirst( strtolower( $this->getTableName() ) ),
				ucfirst( strtolower( $columnName ) ),
				$actual->getValue( 0, 'DATA_PRECISION' ),
				$actual->getValue( 0, 'DATA_SCALE' ),
				$this->markAdjustments['incorrectLength']
			);
							
			if ( $minLength > 0 )
			{
				$this->assertGreaterThanOrEqual( $minLength, $actual->getValue( 0, 'DATA_PRECISION' ), $errorString );
			}
							
			if ( $maxLength > 0 )
			{
				$this->assertLessThanOrEqual( $maxLength, $actual->getValue( 0, 'DATA_PRECISION' ), $errorString );
			}
						
			$this->assertEquals( $numDecimals, $actual->getValue( 0, 'DATA_SCALE' ), $errorString );
		}
		else
		{
			// We might encounter CLOBs as an alternative for a large VARCHAR2.
			// Ignore these, as they have no particular length. BLOBs, DATEs and
			// standalone CLOBs should never show up in the list in the first place,
			// as they should have no length specified.
			if ( $actual->getValue( 0, 'DATA_TYPE' ) != 'CLOB' )
			{
				$errorString = sprintf(
					'column %s.%s has incorrect length %d [%+1.1f]',
					ucfirst( strtolower( $this->getTableName() ) ),
					ucfirst( strtolower( $columnName ) ),
					$actual->getValue( 0, 'CHAR_LENGTH' ),
					$this->markAdjustments['incorrectLength']
				);
								
				if ( $maxLength > 0 )
				{
					$this->assertLessThanOrEqual( $maxLength, $actual->getValue( 0, 'CHAR_LENGTH' ), $errorString );
				}
				if ( $minLength > 0 )
				{
					$this->assertGreaterThanOrEqual( $minLength, $actual->getValue( 0, 'CHAR_LENGTH' ), $errorString );
				}
								
			}
		}
	}
	
	
	/**
	 *	Assert that a column allows or disallows nulls.
	 *
	 *	This queries Oracle's User_Tab_Cols data dictionary view and compares the nullability for the specified column of the current table with the expected column nullability. Tests that use this should use provideColumnNullabilities as their data provider.
	 *
	 *	@access protected
	 *	@return void
	 */
	protected function assertColumnNullability( $columnName, $columnNullability )
	{
// 		echo "\n[[ Testing whether " . ucfirst( strtolower( $this->getTableName() ) ) . '.' . ucfirst( strtolower( $columnName ) ) . " nullability is " . $columnNullability . " ]]\n";
		self::$reporter->report( Reporter::STATUS_TEST, "[[ %s.%s nulls: %s ]] ",
			array( ucfirst( strtolower( $this->getTableName() ) ), ucfirst( strtolower( $columnName ) ), $columnNullability ) );
		
		$queryString = sprintf(
			"SELECT Nullable
			 FROM User_Tab_Cols
			 WHERE ( Table_Name = '%s' ) AND ( Column_Name = '%s' )",
			strtoupper( $this->getTableName() ),
			strtoupper( $columnName )
		);
//	 	echo "$queryString\n";
		
		$actual = $this->getConnection()->createQueryTable( $this->getTableName() . '_' . $columnName, $queryString );
		
		$errorString = sprintf(
			'column %s.%s has incorrect nullability "%s" [%+1.1f]',
			ucfirst( strtolower( $this->getTableName() ) ),
			ucfirst( strtolower( $columnName ) ),
			$actual->getValue( 0, 'NULLABLE' ),
			$this->markAdjustments['incorrectNullability']
		);
		
		$this->assertEquals( $actual->getValue( 0, 'NULLABLE' ), $columnNullability, $errorString );
	}
	
	
	/**
	 *	Assert that a column accepts a particular legal value.
	 *
	 *	This attempts to insert a known legal value into a particular column of the current table, which should succeed. This should only be applied to columns with an enumerated set of possible values. Tests that use this should use provideColumnLegalValues as their data provider.
	 *
	 *	@access protected
	 *	@return void
	 */
	protected function assertColumnLegalValue( $columnName, $legalValue )
	{
		if ( $columnName == '___NO_DATA___' )
		{
			$this->markTestSkipped( 'no columns with enmuerated legal values' );
		}
	
// 		echo "\n[[ Testing whether " . ucfirst( strtolower( $this->getTableName() ) ) . '.' . ucfirst( strtolower( $columnName ) ) . " accepts legal value [" . $legalValue . "] ]]\n";
		self::$reporter->report( Reporter::STATUS_TEST, "[[ %s.%s accepts “%s” ]] ",
			array( ucfirst( strtolower( $this->getTableName() ) ), ucfirst( strtolower( $columnName ) ), $legalValue ) );
		
		$substitutions[$columnName] = $legalValue;
		$insertString = $this->constructInsert( $substitutions );
		
 		$stmt = $this->getConnection()->getConnection()->prepare( $insertString );
		
		$errorString = sprintf(
			"column %s.%s won't accept legal value %s [%+1.1f]",
			ucfirst( strtolower( $this->getTableName() ) ),
			ucfirst( strtolower( $columnName ) ),
			$legalValue,
			$this->markAdjustments['incorrectCheck']
		);
						
		$this->assertTrue( $stmt->execute(), $errorString );
	}
	
	
	/**
	 *	Assert that a text column rejects a particular illegal value, implicitly enforced by exceeding the column length.
	 *
	 *	This attempts to insert a known illegal value into a particular text column of the current table, which should fail because it's larger than the specified column length. (Relying on this kind of implicit enforcement is bad practice in general, as the column length can be changed, but it's better than no enforcement at all!). This should only be applied to columns with an enumerated set of possible values. Tests that use this should use provideColumnIllegalValues as their data provider. Tests will also need to include the following expected exception annotations:
	 *
	 *	@expectedException PDOException
	 *	@expectedExceptionMessage length exceeded
	 *	@expectedExceptionCode HY000
	 *
	 *	@access protected
	 *	@return void
	 */
	protected function assertColumnIllegalValueImplicit( $columnName, $illegalValue )
	{
		if ( $columnName == '___NO_DATA___' )
		{
			$this->markTestSkipped( 'no columns with enmuerated illegal values' );
		}
	
// 		echo "\n[[ Testing whether " . ucfirst( strtolower( $this->getTableName() ) ) . '.' . ucfirst( strtolower( $columnName ) ) . " rejects illegal value [" . $illegalValue . "] using the column length (implicit) ]]\n";
		self::$reporter->report( Reporter::STATUS_TEST, "[[ %s.%s rejects “%s” using column length (implicit) ]] ",
			array( ucfirst( strtolower( $this->getTableName() ) ), ucfirst( strtolower( $columnName ) ), $illegalValue ) );
		
		$substitutions[$columnName] = $illegalValue;
		$insertString = $this->constructInsert( $substitutions );
		
 		$stmt = $this->getConnection()->getConnection()->prepare( $insertString );
		
		$errorString = sprintf(
			"column %s.%s accepts illegal value %s [%+1.1f]",
			ucfirst( strtolower( $this->getTableName() ) ),
			ucfirst( strtolower( $columnName ) ),
			$illegalValue,
			$this->markAdjustments['incorrectCheck']
		);
						
	 	/*	Note that if the column being tested is a number or date, the error returned will be "value larger than specified precision", whereas for text columns, the error returned will be "value too large for column". We therefore need to manually catch the exception and throw a new "length exceeded" exception for these two cases. Otherwise we just let the exception propagate up the chain as normal. This somewhat subverts the normal unit testing methodology of one case per test, but this is really a single "logical" test case. Plus this isn't really a conventional use of unit testing anyway!
		*/
		try
		{
			$this->assertTrue( $stmt->execute(), $errorString );
		}
		catch ( PDOException $e )
		{
			if ( ( strpos( $e->getMessage(), "value larger than specified precision" ) !== FALSE ) ||
			     ( strpos( $e->getMessage(), "value too large for column" ) !== FALSE ) )
			{
				throw new PDOException( "length exceeded" );
			}
			else
			{
				throw $e;
			}
		}
	}
	
	
	/**
	 *	Assert that a text column rejects a particular illegal value, explicitly enforced by a CHECK constraint.
	 *
	 *	This attempts to insert a known illegal value into a particular text column of the current table, which should fail with a CHECK constraint violation. This should only be applied to columns with an enumerated set of possible values. Tests that use this should use provideColumnIllegalValues as their data provider. Tests will also need to include the following expected exception annotations:
	 *
	 *	@expectedException PDOException
	 *	@expectedExceptionMessage check constraint
	 *	@expectedExceptionCode HY000
	 *
	 *	@access protected
	 *	@return void
	 */
	protected function assertColumnIllegalValueExplicit( $columnName, $illegalValue )
	{
		if ( $columnName == '___NO_DATA___' )
		{
			$this->markTestSkipped( 'no columns with enmuerated illegal values' );
		}
	
// 		echo "\n[[ Testing whether " . ucfirst( strtolower( $this->getTableName() ) ) . '.' . ucfirst( strtolower( $columnName ) ) . " rejects illegal value [" . $illegalValue . "] using a CHECK constraint ]]\n";
		self::$reporter->report( Reporter::STATUS_TEST, "[[ %s.%s rejects “%s” using CHECK ]] ",
			array( ucfirst( strtolower( $this->getTableName() ) ), ucfirst( strtolower( $columnName ) ), $illegalValue ) );
		
		$substitutions[$columnName] = $illegalValue;
		$insertString = $this->constructInsert( $substitutions );
		
 		$stmt = $this->getConnection()->getConnection()->prepare( $insertString );
		
		$errorString = sprintf(
			"column %s.%s accepts illegal value %s [%+1.1f]",
			ucfirst( strtolower( $this->getTableName() ) ),
			ucfirst( strtolower( $columnName ) ),
			$illegalValue,
			$this->markAdjustments['incorrectCheck']
		);
						
		$this->assertTrue( $stmt->execute(), $errorString );
	}
	
	
	/**
	 *	Assert that a column only accepts values greater than its underflow value, explicitly enforced by a CHECK constraint.
	 *
	 *	This attempts to insert a known illegal underflow value into a particular column of the current table, which should fail with a CHECK constraint violation. This should only be applied to columns with a continuous range of values, usually numbers and dates. Tests that use this should use provideColumnUnderflowValues as their data provider. Tests will also need to include the following expected exception annotations:
	 *
	 *	@expectedException PDOException
	 *	@expectedExceptionMessage check constraint
	 *	@expectedExceptionCode HY000
	 *
	 *	Note that there's no need for explicit/implicit variants like there is with overflow values, as an underflow value should never be rejected by exceeding the column length. Something much more fundamental is wrong if this happens!
	 *
	 *	@access protected
	 *	@return void
	 */
	protected function assertColumnUnderflowValue( $columnName, $underflowValue )
	{
		if ( $columnName == '___NO_DATA___' )
		{
			$this->markTestSkipped( 'no columns with underflow values' );
		}
	
		self::$reporter->report( Reporter::STATUS_TEST, "[[ %s.%s rejects values ≤ %s using CHECK ]] ",
			array( ucfirst( strtolower( $this->getTableName() ) ), ucfirst( strtolower( $columnName ) ), $underflowValue ) );
		
		$substitutions[$columnName] = $underflowValue;
		$insertString = $this->constructInsert( $substitutions );
		
 		$stmt = $this->getConnection()->getConnection()->prepare( $insertString );
		
		$errorString = sprintf(
			"column %s.%s accepts illegal values <= %s [%+1.1f]",
			ucfirst( strtolower( $this->getTableName() ) ),
			ucfirst( strtolower( $columnName ) ),
			$underflowValue,
			$this->markAdjustments['incorrectCheck']
		);
						
		$this->assertTrue( $stmt->execute(), $errorString );
	}
	
	
	/**
	 *	Assert that a column only accepts values less than its overflow value, implicitly enforced by exceeding the column length.
	 *
	 *	This attempts to insert a known illegal overflow value into a particular column of the current table, which should fail because it's larger than the specified column length. (Relying on this kind of implicit enforcement is bad practice in general, as the column length can be changed, but it's better than no enforcement at all!) This should only be applied to columns with a continuous range of values, usually numbers and dates. Tests that use this should use provideColumnOverflowValues as their data provider. Tests will also need to include the following expected exception annotations:
	 *
	 *	@expectedException PDOException
	 *	@expectedExceptionMessage length exceeded
	 *	@expectedExceptionCode HY000
	 *
	 *	@access protected
	 *	@return void
	 */
	protected function assertColumnOverflowValueImplicit( $columnName, $overflowValue )
	{
		if ( $columnName == '___NO_DATA___' )
		{
			$this->markTestSkipped( 'no columns with overflow values' );
		}
	
		self::$reporter->report( Reporter::STATUS_TEST, "[[ %s.%s rejects values ≥ %s using column length (implicit) ]] ",
			array( ucfirst( strtolower( $this->getTableName() ) ), ucfirst( strtolower( $columnName ) ), $overflowValue ) );
		
		$substitutions[$columnName] = $overflowValue;
		$insertString = $this->constructInsert( $substitutions );
		
 		$stmt = $this->getConnection()->getConnection()->prepare( $insertString );
		
		$errorString = sprintf(
			"column %s.%s accepts illegal values >= %s [%+1.1f]",
			ucfirst( strtolower( $this->getTableName() ) ),
			ucfirst( strtolower( $columnName ) ),
			$overflowValue,
			$this->markAdjustments['incorrectCheck']
		);
		
	 	/*	Note that if the column being tested is a number or date, the error returned will be "value larger than specified precision", whereas for text columns, the error returned will be "value too large for column". We therefore need to manually catch the exception and throw a new "length exceeded" exception for these two cases. Otherwise we just let the exception propagate up the chain as normal. This somewhat subverts the normal unit testing methodology of one case per test, but this is really a single "logical" test case. Plus this isn't really a conventional use of unit testing anyway!
		*/
		try
		{
			$this->assertTrue( $stmt->execute(), $errorString );
		}
		catch ( PDOException $e )
		{
			if ( ( strpos( $e->getMessage(), "value larger than specified precision" ) !== FALSE ) ||
			     ( strpos( $e->getMessage(), "value too large for column" ) !== FALSE ) )
			{
				throw new PDOException( "length exceeded" );
			}
			else
			{
				throw $e;
			}
		}
	}
	
	
	/**
	 *	Assert that a column only accepts values less than its overflow value, explicitly enforced by a CHECK constraint.
	 *
	 *	This attempts to insert a known illegal overflow value into a particular column of the current table, which should fail with a CHECK constraint violation. This should only be applied to columns with a continuous range of values, usually numbers and dates. Tests that use this should use provideColumnOverflowValues as their data provider. Tests will also need to include the following expected exception annotations:
	 *
	 *	@expectedException PDOException
	 *	@expectedExceptionMessage check constraint
	 *	@expectedExceptionCode HY000
	 *
	 *	@access protected
	 *	@return void
	 */
	protected function assertColumnOverflowValueExplicit( $columnName, $overflowValue )
	{
		if ( $columnName == '___NO_DATA___' )
		{
			$this->markTestSkipped( 'no columns with overflow values' );
		}
	
		self::$reporter->report( Reporter::STATUS_TEST, "[[ %s.%s rejects values ≥ %s using CHECK ]] ",
			array( ucfirst( strtolower( $this->getTableName() ) ), ucfirst( strtolower( $columnName ) ), $overflowValue ) );
		
		$substitutions[$columnName] = $overflowValue;
		$insertString = $this->constructInsert( $substitutions );
		
 		$stmt = $this->getConnection()->getConnection()->prepare( $insertString );
		
		$errorString = sprintf(
			"column %s.%s accepts illegal values >= %s [%+1.1f]",
			ucfirst( strtolower( $this->getTableName() ) ),
			ucfirst( strtolower( $columnName ) ),
			$overflowValue,
			$this->markAdjustments['incorrectCheck']
		);
		
		$this->assertTrue( $stmt->execute(), $errorString );
	}
	
	
	/**
	 *	Assert that the primary key constraint of a table exists.
	 *
	 *	This queries Oracle's User_Constraints data dictionary view for a constraint of type 'P' on the current table. It returns the name of the primary key constraint.
	 *
	 *	@access protected
	 *	@return string
	 */
	public function assertPKExists()
	{
// 		echo "\n[[ Testing whether " . ucfirst( strtolower( $this->getTableName() ) ) . " table primary key constraint exists ]]\n";
		self::$reporter->report( Reporter::STATUS_TEST, "[[ %s PK ]] ",
			array( ucfirst( strtolower( $this->getTableName() ) ) ) );
		
		$queryString = sprintf(
			"SELECT Constraint_Name
			 FROM User_Constraints
			 WHERE ( Table_Name = '%s' ) AND ( Constraint_Type = 'P' )",
			strtoupper( $this->getTableName() )
		);
						
		$actual = $this->getConnection()->createQueryTable( $this->getTableName() . '_PK', $queryString );

		$errorString = sprintf(
			"couldn't find a PK constraint for %s [%+1.1f]",
			ucfirst( strtolower( $this->getTableName() ) ),
			$this->markAdjustments['incorrectPK']
		);
						
		$this->assertEquals( 1, $actual->getRowCount(), $errorString );
		
		return $actual->getValue( 0, 'CONSTRAINT_NAME' );
	}
	
	
	/**
	 *	Assert that the primary key constraint of a table includes the correct columns.
	 *
	 *	Tests that use this must depend on a test that calls assertPKExists(), which returns the name of the PK constraint. We can query User_Cons_Columns to see whether the lists match.
	 *
	 *	@access protected
	 *	@return void
	 */
	public function assertPKColumns( $constraintName )
	{
		$tableName = $this->getTableName() . '_PK_cols';
		$expected = $this->getPKColumnListAsDataSet( $tableName );
		
// 		echo "\n[[ Testing whether " . ucfirst( strtolower( $this->getTableName() ) ) .
// 			" table primary key constraint contains (only) the column";
// 		if ( count( $this->getPKColumnlist() ) > 1 )
// 		{
// 			echo "s";
// 		}
// 		echo " " . ucwords( strtolower( implode( ', ', $this->getPKColumnList() ) ) ) . " ]]\n";
		self::$reporter->report( Reporter::STATUS_TEST, "[[ %s PK: %s ]] ",
			array( ucfirst( strtolower( $this->getTableName() ) ), ucwords( strtolower( implode( ', ', $this->getPKColumnList() ) ) ) ) );

		$queryString = sprintf(
			"SELECT Column_Name
			 FROM User_Cons_Columns
			 WHERE ( Constraint_Name = '%s' )
			 ORDER BY Position",
			strtoupper( $constraintName )
		);
						
		$actual = $this->getConnection()->createQueryTable( $tableName, $queryString );

		$errorString = sprintf(
			"the PK constraint for %s has incorrect columns [%+1.1f]",
			ucfirst( strtolower( $this->getTableName() ) ),
			$this->markAdjustments['incorrectPK']
		);
						
		$this->assertTablesEqual( $expected->getTable( $tableName ), $actual, $errorString );
	}
	
	
	/**
	 *	Assert that the foreign key constraint(s) of a table exist.
	 *
	 *	This queries Oracle's User_Constraints data dictionary view for a constraint of type 'R' on the current table that references the specified table. Tests that use this should use provideFKReferencedTables as their data provider.
	 *
	 *	@access protected
	 *	@return void
	 */
	public function assertFKsExist( $referencedTableName )
	{
		self::$reporter->report( Reporter::STATUS_TEST, "[[ %s FK → %s ]] ",
			array( ucfirst( strtolower( $this->getTableName() ) ), ucfirst( strtolower( $referencedTableName ) ) ) );
		
		$queryString = sprintf(
			"SELECT Child.Constraint_Name
			 FROM User_Constraints Child INNER JOIN User_Constraints Parent
			      ON ( Child.R_Constraint_Name = Parent.Constraint_Name )
			 WHERE ( Child.Table_Name = '%s' )
			   AND ( Parent.Table_Name = '%s' )
			   AND ( Child.Constraint_Type = 'R' )",
			strtoupper( $this->getTableName() ),
			strtoupper( $referencedTableName )
		);
						
		$actual = $this->getConnection()->createQueryTable( $this->getTableName() . '_FK', $queryString );

		$errorString = sprintf(
			"couldn't find a FK constraint for %s referencing %s [%+1.1f]",
			ucfirst( strtolower( $this->getTableName() ) ),
			ucfirst( strtolower( $referencedTableName ) ),
			$this->markAdjustments['incorrectPK']
		);
						
		$this->assertEquals( 1, $actual->getRowCount(), $errorString );
	}
	
	
	/**
	 *	Assert that the foreign key constraints of a table include the correct columns.
	 *
	 *	We can query User_Cons_Columns to see whether the lists match. Tests that use this should use provideFKReferencedTables as their data provider.
	 *
	 *	@access protected
	 *	@return void
	 */
	public function assertFKColumns( $referencedTableName )
	{
		$tableName = $referencedTableName . '_FK_cols';
		$expected = $this->getFKColumnListForTableAsDataSet( $referencedTableName, $tableName );
		$fkColumns = $this->getFKColumnlist();
		
		self::$reporter->report( Reporter::STATUS_TEST, "[[ %s FK → %s: %s ]] ",
			array(	ucfirst( strtolower( $this->getTableName() ) ),
					ucfirst( strtolower( $referencedTableName ) ),
	 				ucwords( strtolower( implode( ', ', $fkColumns[$referencedTableName] ) ) ) ) );
		if ( count( $this->getFKColumnlist() ) > 1 )
		{
			echo "s";
		}
		echo " " . ucwords( strtolower( implode( ', ', $this->getFKColumnListForTable( $referencedTableName ) ) ) ) . " ]]\n";

		$queryString = sprintf(
			"SELECT User_Cons_Columns.Column_Name
			 FROM User_Constraints Child INNER JOIN User_Constraints Parent
			        ON ( Child.R_Constraint_Name = Parent.Constraint_Name )
			      INNER JOIN User_Cons_Columns
			        ON ( Child.Constraint_Name = User_Cons_Columns.Constraint_Name )
			 WHERE ( Child.Table_Name = '%s' )
			   AND ( Parent.Table_Name = '%s' )
			   AND ( Child.Constraint_Type = 'R' )
			 ORDER BY User_Cons_Columns.Position",
			strtoupper( $this->getTableName() ),
			strtoupper( $referencedTableName ),
			strtoupper( implode( "', '", $this->getFKColumnListForTable( $referencedTableName ) ) )
		);
						
		$actual = $this->getConnection()->createQueryTable( $tableName, $queryString );
		
		// Note that we can't use the same trick as for PKs of the second test depending on the first, as this
		// doesn't work when the first test is iterated by a data provider :(. (This probably makes sense when
		// you think about how test execution works in general.) We also can't directly access the protected
		// "data" member of the query table, so we resort to checking whether the row count is zero and skipping
		// the test if so.
		if ( $actual->getRowCount() == 0 ) $this->markTestSkipped( 'FK is missing anyway' );

		$errorString = sprintf(
			"the FK constraint for %s has incorrect columns [%+1.1f]",
			ucfirst( strtolower( $this->getTableName() ) ),
			$this->markAdjustments['unnamedConstraint']
		);
						
		$this->assertTablesEqual( $expected->getTable( $tableName ), $actual, $errorString );
	}
	
	
	/**
	 *	Assert that a constraint of a table has been explicitly named.
	 *
	 *	If the constraint name starts with "SYS_", then it hasn't been explicitly named. Tests that use this should use provideConstraintNames as their data provider.
	 *
	 *	@access protected
	 *	@return void
	 */
	public function assertConstraintNamed( $constraintName, $constraintType )
	{
		if ( $constraintName == '___NO_DATA___' )
		{
			$this->markTestSkipped( 'no constraints to be tested on this table' );
		}
	
		switch ( $constraintType )
		{
			case 'C':
				$longType = 'check';
				break;
			
			case 'P':
				$longType = 'primary key';
				break;
			
			case 'R':
				$longType = 'foreign key';
				break;
			
			case 'U':
				$longType = 'unique';
				break;
			
			default:
				$longtype = "unknown (${constraintType})";
				break;
		}
	
// 		echo "\n[[ Testing whether " . ucfirst( strtolower( $this->getTableName() ) ) .
// 			" table " . $longType . " constraint " . $constraintName . " is explicitly named ]]\n";
		self::$reporter->report( Reporter::STATUS_TEST, "[[ %s %s constraint %s ]] ",
			array( ucfirst( strtolower( $this->getTableName() ) ), $longType, $constraintName ) );

		$errorString = sprintf(
			"the %s constraint %s for %s hasn't been explicitly named [%+1.1f]",
			$longType,
			$constraintName,
			ucfirst( strtolower( $this->getTableName() ) ),
			$this->markAdjustments['unnamedConstraint']
		);
						
		$this->assertNotRegExp( '/^SYS_/', $constraintName, $errorString );
	}
	

	/**
	 * @expectedException PDOException
	 * @expectedExceptionMessage unique constraint
	 * @expectedExceptionCode HY000
	 */
//	 protected function testPrimaryKeyUnique()
//	 {
//	 	echo "\n[[ Testing whether " . ucfirst( strtolower( $this->getTableName() ) ) . " table primary key (UNIQUE) ]]\n";
//	 	$stmt = $this->getConnection()->getConnection()->prepare( "INSERT INTO $this->getTableName() VALUES ( 326, 'foo', 'bar', '1234567', 'baz', 'Manufacturing', 'Technician', 12345, 'quux' )" );
//	 	$this->assertTrue( $stmt->execute(), ucfirst( strtolower( $this->getTableName() ) ) . " PK constraint is missing or incorrectly implemented (permits duplicates) [-1]" );
//	 }
	
	/**
	 * @expectedException PDOException
	 * @expectedExceptionMessage cannot insert NULL into
	 * @expectedExceptionCode HY000
	 */
//	 protected function testPrimaryKeyNotNull()
//	 {
//	 	echo "\n[[ Testing whether " . ucfirst( strtolower( $this->getTableName() ) ) . " table primary key (NOT NULL) ]]\n";
//	 	$stmt = $this->getConnection()->getConnection()->prepare( "INSERT INTO $this->getTableName() VALUES ( null, 'foo', 'bar', '1234567', 'baz', 'Manufacturing', 'Technician', 12345, 'quux' )" );
//	 	$this->assertTrue( $stmt->execute(), ucfirst( strtolower( $this->getTableName() ) ) . " PK constraint is missing or incorrectly implemented (permits nulls) [-1]" );
//	 }
	
	/**
	 * @expectedException PDOException
	 * @expectedExceptionMessage invalid number
	 * @expectedExceptionCode HY000
	 */
//	 protected function testStaffIdDataType()
//	 {
//	 	echo "\n[[ Testing whether " . ucfirst( strtolower( $this->getTableName() ) ) . ".Staff_ID data type (NUMBER) ]]\n";
//	 	$stmt = $this->getConnection()->getConnection()->prepare( "INSERT INTO $this->getTableName() VALUES ( 'abc', 'foo', 'bar', '1234567', 'baz', 'Manufacturing', 'Technician', 12345, 'quux' )" );
//	 	$this->assertTrue( $stmt->execute(), ucfirst( strtolower( $this->getTableName() ) ) . '.Staff_ID data type is not NUMBER [-1]' );
//	 }
	
	/**
	 * expectedException PDOException
	 * expectedExceptionMessage invalid number
	 * expectedExceptionCode HY000
	 */
//	 protected function testStaffIdMaximumValue()
//	 {
//	 	echo "\n[[ Testing whether " . ucfirst( strtolower( $this->getTableName() ) ) . ".Staff_ID maximum value (9999999) ]]\n";
//	 	$stmt = $this->getConnection()->getConnection()->prepare( "INSERT INTO $this->getTableName() VALUES ( 9999999, 'foo', 'bar', '1234567', 'baz', 'Manufacturing', 'Technician', 12345, 'quux' )" );
//	 	$this->assertTrue( $stmt->execute(), ucfirst( strtolower( $this->getTableName() ) ) . '.Staff_ID size is too small (< 7 digits) [-0.5]' );
//	 }
	
	/**
	 * @expectedException PDOException
	 * @expectedExceptionMessage value larger than specified precision allowed for this column
	 * @expectedExceptionCode HY000
	 */
//	 protected function testStaffIdMaximumSize()
//	 {
//	 	echo "\n[[ Testing whether " . ucfirst( strtolower( $this->getTableName() ) ) . ".Staff_ID maximum size (7 digits) ]]\n";
//	 	$stmt = $this->getConnection()->getConnection()->prepare( "INSERT INTO $this->getTableName() VALUES ( 99999999, 'foo', 'bar', '1234567', 'baz', 'Manufacturing', 'Technician', 12345, 'quux' )" );
//	 	$this->assertTrue( $stmt->execute(), ucfirst( strtolower( $this->getTableName() ) ) . '.Staff_ID size is too large (> 7-digits) [-0.5]' );
//	 }
}
?>

