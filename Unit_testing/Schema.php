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
     * Asserts that two variables are equal.
     *
     * @param  mixed   $expected
     * @param  mixed   $actual
     * @param  string  $message
     * @param  float   $delta
     * @param  integer $maxDepth
     * @param  boolean $canonicalize
     * @param  boolean $ignoreCase
     */
    public static function assertEqualsSC($expected, $actual, $message = '', $delta = 0, $maxDepth = 10, $canonicalize = FALSE, $ignoreCase = FALSE)
    {
        $constraint = new PHPUnit_Framework_Constraint_IsEqualSC(
          $expected, $delta, $maxDepth, $canonicalize, $ignoreCase
        );

        self::assertThat($actual, $constraint, $message);
    }


    /**
     * Asserts that a value is greater than or equal to another value.
     *
     * @param  mixed   $expected
     * @param  mixed   $actual
     * @param  string  $message
     * @since  Method available since Release 3.1.0
     */
    public static function assertGreaterThanOrEqualSC($expected, $actual, $message = '')
    {
        self::assertThat(
          $actual, self::greaterThanOrEqualSC($expected), $message
        );
    }

	
    /**
     * Asserts that a value is smaller than or equal to another value.
     *
     * @param  mixed   $expected
     * @param  mixed   $actual
     * @param  string  $message
     * @since  Method available since Release 3.1.0
     */
    public static function assertLessThanOrEqualSC($expected, $actual, $message = '')
    {
        self::assertThat($actual, self::lessThanOrEqualSC($expected), $message);
    }


    /**
     * Returns a PHPUnit_Framework_Constraint_Or matcher object that wraps
     * a PHPUnit_Framework_Constraint_IsEqual and a
     * PHPUnit_Framework_Constraint_GreaterThan matcher object.
     *
     * @param  mixed $value
     * @return PHPUnit_Framework_Constraint_Or
     * @since  Method available since Release 3.1.0
     */
    public static function greaterThanOrEqualSC($value)
    {
        return self::logicalOr(
          new PHPUnit_Framework_Constraint_IsEqualSC($value),
          new PHPUnit_Framework_Constraint_GreaterThanSC($value)
        );
    }


    /**
     * Returns a PHPUnit_Framework_Constraint_Or matcher object that wraps
     * a PHPUnit_Framework_Constraint_IsEqual and a
     * PHPUnit_Framework_Constraint_LessThan matcher object.
     *
     * @param  mixed $value
     * @return PHPUnit_Framework_Constraint_Or
     * @since  Method available since Release 3.1.0
     */
    public static function lessThanOrEqualSC($value)
    {
        return self::logicalOr(
          new PHPUnit_Framework_Constraint_IsEqualSC($value),
          new PHPUnit_Framework_Constraint_LessThanSC($value)
        );
    }


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
	
	
	/**
	 *	Return a list of the table's UNIQUE constraint column names.
	 *
	 *	This should return a list of column names for each UNIQUE constraint.
	 *
	 *	@return array( array( string+ ) )
	 */
	abstract protected function getUniqueColumnList();
	
	
	/**
	 *	Return whether or not the fixture should be loaded.
	 *
	 *	@return boolean
	 */
	abstract protected function willLoadFixture();
	
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
	 *	Return the list of column names for each unique constraint as an array data set.
	 *
	 *	The argument specifies the name of the table in the dataset.
	 *
	 *	@return SchemaTesting_DbUnit_ArrayDataSet
	 */
	protected function getUniqueColumnListAsDataSet( $datasetTableName )
	{
		$theList = array();
		$uniqueColumns = $this->getUniqueColumnList();
		
		foreach ( $uniqueColumns as $constraint  )
		{
		    foreach ( $constraint as $key => $columnName )
		    {
			    array_push( $theList, array( 'COLUMN_NAME' => $columnName, 'POSITION' => $key + 1 ) );
		    }
		}
		
		return new SchemaTesting_DbUnit_ArrayDataSet( array( $datasetTableName => $theList ) );
	}
	
	
	/**
	 *	Return a list of aliases for a given column name (if any).
	 *
	 *	@return array
	 */
	protected function getColumnAliases( $columnName )
	{
		$theColumns = $this->getColumnList();
		
		if ( isset( $theColumns[$columnName]['aliases'] ) )
		{
			return $theColumns[$columnName]['aliases'];
		}
		return array();
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
	 *	Return the appropriate fixture setup operation, depending on whether the fixture will be loaded.
	 *
	 *	@access protected
	 *	@return PHPUnit_Extensions_Database_Operation_DatabaseOperation
	 */
	protected function getSetUpOperation()
	{
		if ( $this->willLoadFixture() )
		{
			// We can't use the default fixture setup operations with Oracle, because TRUNCATE doesn't work on
			// tables that are referenced by foreign keys, even if the table is empty! We use the DELETE_ALL
			// operation instead.
			return new PHPUnit_Extensions_Database_Operation_Composite(
				array(
					PHPUnit_Extensions_Database_Operation_Factory::DELETE_ALL(),
					PHPUnit_Extensions_Database_Operation_Factory::INSERT()
				)
			);
		}
		else
		{
			// If we're testing whether a table exists, and it doesn't, attempting to set up the fixture
			// will fail with a nasty SQL error! So we just zero out the operation.
			return new PHPUnit_Extensions_Database_Operation_Composite( array() );
		}
	}
	
	
	/**
	 *	Return the appropriate fixture teardown operation, depending on whether the fixture will be loaded.
	 *
	 *	We can't use the standard fixture teardown operation with Oracle, because TRUNCATE doesn't work on tables that are referenced by foreign keys, even if the table is empty! We use the DELETE_ALL action operation instead.
	 *
	 *	@access protected
	 *	@return PHPUnit_Extensions_Database_Operation_DatabaseOperation
	 */
	protected function getTearDownOperation()
	{
		if ( $this->willLoadFixture() )
		{
			// We can't use the default fixture teardown operations with Oracle, because TRUNCATE doesn't work on
			// tables that are referenced by foreign keys, even if the table is empty! We use the DELETE_ALL
			// operation instead.
			return new PHPUnit_Extensions_Database_Operation_Composite( array( PHPUnit_Extensions_Database_Operation_Factory::DELETE_ALL() ) );
		}
		else
		{
			// If we're testing whether a table exists, and it doesn't, attempting to tear down the fixture
			// will fail with a nasty SQL error! So we just zero out the operation.
			return new PHPUnit_Extensions_Database_Operation_Composite( array() );
		}
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
			// If min_length is null, then the length is effectively unlimited (implying that no length should be imposed.)
			if ( is_null( $minLength ) || ( $minLength > 0 ) || ( $maxLength > 0 ) )
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
	 *	Data provider to return a list of all columns that should have defaults.
	 *
	 *	If your test needs to iterate through all column defaults of a table, then use this method as the data provider. Each column name is presented to the consumer in turn.
	 *
	 *	@access public
	 *	@return array( array( string, string )* )
	 */
	public function provideColumnDefaults()
	{
		$theList = array();
		foreach ( $this->getColumnList() as $columnName => $columnDetails )
		{
		    if ( isset( $columnDetails['default'] ) )
    			array_push( $theList, array( $columnName, $columnDetails['default'] ) );
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
		//
		// I thought it might be possible to use a WITH to solve the problem, but it
		// turns out it doesn't work :(.
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
	
	
	/**
	 *	Data provider to return a list of referenced table and columns for each foreign key.
	 *
	 *	If your test needs to iterate through all of the referenced tables and columns of the table's foreign keys, then use this method as the data provider. Each referenced table name plus a list of the referencing columns is presented to the consumer in turn.
	 *
	 *	@access public
	 *	@return array( array( string, array( string+ ) )* )
	 */
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
		self::$reporter->report( Reporter::STATUS_TEST, "[[ %s ]] ", array( ucfirst( strtolower( $this->getTableName() ) ) ) );
		
		$queryString = sprintf(
			"SELECT Table_Name
			 FROM User_Tables
			 WHERE ( Table_Name = '%s' )",
			strtoupper( $this->getTableName() )
		);
		
		if ( RUN_MODE === 'staff' )
		{
			$errorString = sprintf(
				"couldn't find the %s table [%+1.1f] --- check for misspelled [%+1.1f] or delimited [%+1.1f] identifiers",
				ucfirst( strtolower( $this->getTableName() ) ),
				$this->markAdjustments['missingTable'],
				$this->markAdjustments['misspelledIdentifier'],
				$this->markAdjustments['delimitedIdentifier']
			);
		}
		else if ( RUN_MODE === 'student' )
		{
			$errorString = sprintf( "couldn't find the %s table", ucfirst( strtolower( $this->getTableName() ) ) );
		}
		
		$actual = $this->getConnection()->createQueryTable( "user_tables", $queryString );
		self::assertEquals( 1, $actual->getRowCount(), $errorString );
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
		self::$reporter->report( Reporter::STATUS_TEST, "[[ %s.%s ]] ",
			array( ucfirst( strtolower( $this->getTableName() ) ), ucfirst( strtolower( $columnName ) ) ) );
		
		$queryString = sprintf(
			"SELECT Column_Name
			 FROM User_Tab_Cols
			 WHERE ( Table_Name = '%s' ) AND ( Column_Name = '%s' )",
			strtoupper( $this->getTableName() ),
			strtoupper( $columnName )
		);
		
		$actual = $this->getConnection()->createQueryTable( $this->getTableName() . '_' . $columnName, $queryString );
		
		if ( RUN_MODE === 'staff' )
		{
			$errorString = sprintf(
				"couldn't find the %s.%s column --- check for misspelled [%+1.1f] or delimited [%+1.1f] identifiers",
				ucfirst( strtolower( $this->getTableName() ) ),
				ucfirst( strtolower( $columnName ) ),
				$this->markAdjustments['misspelledIdentifier'],
				$this->markAdjustments['delimitedIdentifier']
			);
		}
		else if ( RUN_MODE === 'student' )
		{
			$errorString = sprintf(	"couldn't find the %s.%s column",
									ucfirst( strtolower( $this->getTableName() ) ),
									ucfirst( strtolower( $columnName ) )	);
		}
		
		$theCount = $actual->getRowCount();
		if ( $theCount === 0 )
		{
			// Column doesn't exist with the expected name; check for aliases.
			$aliases = $this->getColumnAliases( $columnName );
			if ( count( $aliases ) > 0 )
			{
				foreach ( $aliases as $alias )
				{
					$queryString = sprintf(
						"SELECT Column_Name
						 FROM User_Tab_Cols
						 WHERE ( Table_Name = '%s' ) AND ( Column_Name = '%s' )",
						strtoupper( $this->getTableName() ),
						strtoupper( $alias )
					);
					$actual = $this->getConnection()->createQueryTable( $this->getTableName() . '_' . $columnName, $queryString );
					if ( $actual->getRowCount() === 1 )
					{
						self::$reporter->report(	Reporter::STATUS_WARNING,
													'Found alternative name “%s” for %s.%s; please rename it to “%s”.',
													array( $alias, $this->getTableName(), $columnName, $columnName )	);
						break;
					}
				}
			}
		}
		
		self::assertEquals( 1, $theCount, $errorString );
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
		if ( RUN_MODE === 'staff' )
		{
			self::$reporter->report(	Reporter::STATUS_TEST, "[[ %s.%s: data type is %s ]] ",
										array(	ucfirst( strtolower( $this->getTableName() ) ),
												ucfirst( strtolower( $columnName ) ),
												implode( ' | ', $columnTypeList ) ) );
		}
		else if ( RUN_MODE === 'student' )
		{
			self::$reporter->report(	Reporter::STATUS_TEST, "[[ %s.%s data type ]] ",
										array( ucfirst( strtolower( $this->getTableName() ) ), ucfirst( strtolower( $columnName ) ) ) );
		}
		
		$queryString = sprintf(
			"SELECT Data_Type
			 FROM User_Tab_Cols
			 WHERE ( Table_Name = '%s' ) AND ( Column_Name = '%s' )",
			strtoupper( $this->getTableName() ),
			strtoupper( $columnName )
		);
		
		$actual = $this->getConnection()->createQueryTable( $this->getTableName() . '_' . $columnName, $queryString );
		
		if ( RUN_MODE === 'staff' )
		{
			$errorString = sprintf(	'column %s.%s has unexpected data type %s [%+1.1f]',
									ucfirst( strtolower( $this->getTableName() ) ),
									ucfirst( strtolower( $columnName ) ),
									$actual->getValue( 0, 'DATA_TYPE' ),
									$this->markAdjustments['incorrectDataType']	);
		}
		else if ( RUN_MODE === 'student' )
		{
			$errorString = sprintf(
				'column %s.%s has unexpected data type %s; check the specification again or consult with the teaching staff',
				ucfirst( strtolower( $this->getTableName() ) ),
				ucfirst( strtolower( $columnName ) ),
				$actual->getValue( 0, 'DATA_TYPE' ),
				$this->markAdjustments['incorrectDataType']	);
		}
						
		self::assertContains( $actual->getValue( 0, 'DATA_TYPE' ), $columnTypeList, $errorString );
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
			$this->markTestSkipped( 'all of the columns are of types that have no length' );
		}
	
		$lengthSpec = '';
		if ( RUN_MODE === 'staff' )
		{
		    if ( is_null( $minLength ) )
		    {
				$lengthSpec .= "is not specified";
		    }
			elseif ( $maxLength == 0 )
			{
				$lengthSpec .= "≥ ${minLength}";
			}
			elseif ( $minLength == 0 )
			{
				$lengthSpec .= "≤ ${maxLength}";
			}
			elseif ( $minLength != $maxLength )
			{
				$lengthSpec .= "${minLength}–${maxLength}";
			}
			else
			{
				$lengthSpec .= "= ${maxLength}";
			}
			
			if ( $columnType === 'NUMBER' )
			{
			    if ( is_null( $numDecimals ) )
			    {
					$lengthSpec .= " (scale not specified ⇒ 0)";
			    }
				else
				{
					$lengthSpec .= " (with scale " . $numDecimals . ")";
				}
			}
		}
		
		self::$reporter->report( Reporter::STATUS_TEST, "[[ %s.%s %s %s ]] ",
			array( ucfirst( strtolower( $this->getTableName() ) ),
			       ucfirst( strtolower( $columnName ) ),
			       ( $columnType === 'NUMBER' ) ? 'precision' : 'length',
			       $lengthSpec ) );
		
		if ( $columnType === 'NUMBER' )
		{
			/*
				Note NVL() on Data_Precision to ensure that we don't get spurious errors for NUMBER 
				columns with unspecified precision (=> Data_Precision is null). 38 is the maximum
				precision for a NUMBER and will always be larger than the specified minimum precision
				(but may be larger than any specified maximum precision). Data_Scale gets NVL'd to
				zero because that's the expected value for no decimal places. If decimal places /are/
				expected, this will still fail.
			*/
			$queryString = sprintf(
				"SELECT Data_Type, NVL( Data_Precision, 38 ) AS Data_Precision, NVL( Data_Scale, 0 ) AS Data_Scale
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
            self::$reporter->report( Reporter::STATUS_DEBUG, "[[ expected: minimum precision = %s, maximum precision = %s, scale = %s ]]",
                array( $minLength, $maxLength, $numDecimals ) );
    		
		    $actualPrecision = $actual->getValue( 0, 'DATA_PRECISION' );
		    $actualScale     = $actual->getValue( 0, 'DATA_SCALE' );
		    
		    self::$reporter->report( Reporter::STATUS_DEBUG, "[[ actual: precision = %s, scale = %s ]] ",
		        array( $actualPrecision, $actualScale ) );
		
			if ( RUN_MODE === 'staff' )
			{
				$errorString = sprintf(	'column %s.%s has unexpected precision and/or scale %d, %d [%+1.1f]',
										ucfirst( strtolower( $this->getTableName() ) ),
										ucfirst( strtolower( $columnName ) ),
										$actualPrecision,
										$actualScale,
										$this->markAdjustments['incorrectLength']	);
			}
			else if ( RUN_MODE === 'student' )
			{
				$errorString = sprintf(
					'column %s.%s has unexpected precision and/or scale; check the specification again or consult with the teaching staff.',
					ucfirst( strtolower( $this->getTableName() ) ),
					ucfirst( strtolower( $columnName ) )	);
			}
							
			if ( $minLength > 0 )
			{
				self::assertGreaterThanOrEqualSC( $minLength, $actualPrecision, $errorString );
			}
							
			if ( $maxLength > 0 )
			{
				self::assertLessThanOrEqualSC( $maxLength, $actualPrecision, $errorString );
			}
						
			self::assertEqualsSC( $numDecimals, $actualScale, $errorString );
		}
		else
		{
			// We might encounter CLOBs as an alternative for a large VARCHAR2.
			// Ignore these, as they have no particular length. BLOBs, DATEs and
			// standalone CLOBs should never show up in the list in the first place,
			// as they should have no length specified.
			if ( $actual->getValue( 0, 'DATA_TYPE' ) != 'CLOB' )
			{
                self::$reporter->report( Reporter::STATUS_DEBUG, "[[ expected: minimum length = %s, maximum length = %2 ]]",
                    array( $minLength, $maxLength ) );

                $actual_length = $actual->getValue( 0, 'CHAR_LENGTH' );
                
                self::$reporter->report( Reporter::STATUS_DEBUG, "[[ actual: length = %s ]] ", array( $actual_length ) );
            
				if ( RUN_MODE === 'staff' )
				{
					$errorString = sprintf(	'column %s.%s has unexpected length %d [%+1.1f]',
											ucfirst( strtolower( $this->getTableName() ) ),
											ucfirst( strtolower( $columnName ) ),
											$actual_length,
											$this->markAdjustments['incorrectLength']	);
				}
				else if ( RUN_MODE === 'student' )
				{
					$errorString = sprintf(
						'column %s.%s has unexpected length; check the specification again or consult with the teaching staff.',
						ucfirst( strtolower( $this->getTableName() ) ),
						ucfirst( strtolower( $columnName ) )	);
				}
								
				if ( $maxLength > 0 )
				{
					self::assertLessThanOrEqualSC( $maxLength, $actual_length, $errorString );
				}
				if ( $minLength > 0 )
				{
					self::assertGreaterThanOrEqualSC( $minLength, $actual_length, $errorString );
				}
								
			}
		}
	}
	
	
	/****************************************************************************************************
	 *  THE REMAINING ASSERT() METHODS ARE NOT INTENDED TO BE CALLED IN "STUDENT" RUN MODE.
	 ****************************************************************************************************/
	
	
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
		self::$reporter->report( Reporter::STATUS_TEST, "[[ %s.%s nullability should be %s ]] ",
			array( ucfirst( strtolower( $this->getTableName() ) ), ucfirst( strtolower( $columnName ) ), $columnNullability ) );
		
		$queryString = sprintf(
			"SELECT Nullable
			 FROM User_Tab_Cols
			 WHERE ( Table_Name = '%s' ) AND ( Column_Name = '%s' )",
			strtoupper( $this->getTableName() ),
			strtoupper( $columnName )
		);
		
		$actual = $this->getConnection()->createQueryTable( $this->getTableName() . '_' . $columnName, $queryString );
		
		$errorString = sprintf(	'column %s.%s has incorrect nullability "%s" [%+1.1f]',
								ucfirst( strtolower( $this->getTableName() ) ),
								ucfirst( strtolower( $columnName ) ),
								$actual->getValue( 0, 'NULLABLE' ),
								$this->markAdjustments['incorrectNullability']	);
		
		self::assertEquals( $actual->getValue( 0, 'NULLABLE' ), $columnNullability, $errorString );
	}
	
	
	/**
	 *	Assert that a column has a default value.
	 *
	 *	This queries Oracle's User_Tab_Cols data dictionary view and checks whether the default values for the specified column of the current table is null. Tests that use this should use provideColumnDefaults as their data provider.
	 *
	 *	@access protected
	 *	@return void
	 */
	protected function assertColumnDefault( $columnName, $columnDefault )
	{
		self::$reporter->report( Reporter::STATUS_TEST, "[[ %s.%s default should be %s ]] ",
			array( ucfirst( strtolower( $this->getTableName() ) ), ucfirst( strtolower( $columnName ) ), $columnDefault ) );
		
		$queryString = sprintf(
			"SELECT Data_Default
			 FROM User_Tab_Cols
			 WHERE ( Table_Name = '%s' ) AND ( Column_Name = '%s' )",
			strtoupper( $this->getTableName() ),
			strtoupper( $columnName )
		);
		
		$actual = $this->getConnection()->createQueryTable( $this->getTableName() . '_' . $columnName, $queryString );
		
		// Defaults for text and (literal) date columns come with single quotes around them. Also, for some reason trim() with ' added to the standard character list won't strip off the trailing quote if there's whitespace following it, so we have to trim twice: once for whitespace, once for the quotes. Grr.
		$actualDefault = trim( trim( $actual->getValue( 0, 'DATA_DEFAULT' ) ), "'" );
		
		$errorString = sprintf(	'column %s.%s has incorrect default "%s" [%+1.1f]',
								ucfirst( strtolower( $this->getTableName() ) ),
								ucfirst( strtolower( $columnName ) ),
								$actualDefault,
								$this->markAdjustments['incorrectDefault']	);
		
		self::assertEquals( $actualDefault, $columnDefault, $errorString );
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
			$this->markTestSkipped( 'no columns with enumerated legal values' );
		}
		
		// Should never be called in student run mode.
		self::$reporter->report( Reporter::STATUS_TEST, "[[ %s.%s accepts “%s” ]] ",
			array( ucfirst( strtolower( $this->getTableName() ) ), ucfirst( strtolower( $columnName ) ), $legalValue ) );
		
		$substitutions[$columnName] = $legalValue;
		$insertString = $this->constructInsert( $substitutions );
		
		self::$reporter->report( Reporter::STATUS_DEBUG, "[[ %s ]] ", array( $insertString ) );
			
 		$stmt = $this->getConnection()->getConnection()->prepare( $insertString );
		
		$errorString = sprintf(
			"column %s.%s won't accept legal value %s [%+1.1f]",
			ucfirst( strtolower( $this->getTableName() ) ),
			ucfirst( strtolower( $columnName ) ),
			$legalValue,
			$this->markAdjustments['incorrectCheck']
		);
						
	 	/*	Note that if the constraint is incorrect (e.g., incorrect capitalisation of the legal values), then we'll get a check constraint violation. We therefore need to manually catch the exception and fail the test. Antyhing else gets thrown up the chain.
		*/
		try
		{
			self::assertTrue( $stmt->execute(), $errorString );
		}
		catch ( PDOException $e )
		{
			if ( ( strpos( $e->getMessage(), "check constraint" ) !== FALSE ) )
			{
				self::assertTrue( FALSE, $errorString );
			}
			else
			{
				throw $e;
			}
		}
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
			$this->markTestSkipped( 'no columns with enumerated illegal values' );
		}
	
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
			self::assertTrue( $stmt->execute(), $errorString );
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
			$this->markTestSkipped( 'no columns with enumerated illegal values' );
		}
	
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
						
		self::assertTrue( $stmt->execute(), $errorString );
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
		
		self::assertTrue( $stmt->execute(), $errorString );
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
			self::assertTrue( $stmt->execute(), $errorString );
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
		
		self::assertTrue( $stmt->execute(), $errorString );
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
						
		self::assertEquals( 1, $actual->getRowCount(), $errorString );
		
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
						
		self::assertTablesEqual( $expected->getTable( $tableName ), $actual, $errorString );
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
						
		self::assertGreaterThan( 0, $actual->getRowCount(), $errorString );
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
			strtoupper( $referencedTableName )
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
						
		self::assertTablesEqual( $expected->getTable( $tableName ), $actual, $errorString );
	}
	
	
	/**
	 *	Assert that the unique constraints of a table include the correct columns.
	 *
	 *	We can query User_Cons_Columns to see whether the lists match. [? ->] Tests that use this should use provideFKReferencedTables as their data provider.
	 *
	 *	@access protected
	 *	@return void
	 */
	public function assertUniqueColumns()
	{
		$tableName = $this->getTableName() . '_unique_cols';
		$expected = $this->getUniqueColumnListAsDataSet( $tableName );
		$uniqueColumns = $this->getUniqueColumnlist();
		
// 		self::$reporter->report( Reporter::STATUS_TEST, "[[ %s FK → %s: %s ]] ",
// 			array(	ucfirst( strtolower( $this->getTableName() ) ),
// 					ucfirst( strtolower( $referencedTableName ) ),
// 	 				ucwords( strtolower( implode( ', ', $fkColumns[$referencedTableName] ) ) ) ) );

		$queryString = sprintf(
			"SELECT User_Cons_Columns.Column_Name, User_Cons_Columns.Position
			 FROM User_Constraints INNER JOIN User_Cons_Columns USING ( Constraint_Name )
			 WHERE ( User_Constraints.Table_Name = '%s' )
			   AND ( User_Constraints.Constraint_Type = 'U' )
			 ORDER BY User_Cons_Columns.Position",
			strtoupper( $this->getTableName() )
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
						
		self::assertTablesEqual( $expected->getTable( $tableName ), $actual, $errorString );
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
	
		self::$reporter->report( Reporter::STATUS_TEST, "[[ %s %s constraint %s ]] ",
			array( ucfirst( strtolower( $this->getTableName() ) ), $longType, $constraintName ) );

		$errorString = sprintf(
			"the %s constraint %s for %s hasn't been explicitly named [%+1.1f]",
			$longType,
			$constraintName,
			ucfirst( strtolower( $this->getTableName() ) ),
			$this->markAdjustments['unnamedConstraint']
		);
						
		self::assertNotRegExp( '/^SYS_/', $constraintName, $errorString );
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
//	 	self::assertTrue( $stmt->execute(), ucfirst( strtolower( $this->getTableName() ) ) . " PK constraint is missing or incorrectly implemented (permits duplicates) [-1]" );
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
//	 	self::assertTrue( $stmt->execute(), ucfirst( strtolower( $this->getTableName() ) ) . " PK constraint is missing or incorrectly implemented (permits nulls) [-1]" );
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
//	 	self::assertTrue( $stmt->execute(), ucfirst( strtolower( $this->getTableName() ) ) . '.Staff_ID data type is not NUMBER [-1]' );
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
//	 	self::assertTrue( $stmt->execute(), ucfirst( strtolower( $this->getTableName() ) ) . '.Staff_ID size is too small (< 7 digits) [-0.5]' );
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
//	 	self::assertTrue( $stmt->execute(), ucfirst( strtolower( $this->getTableName() ) ) . '.Staff_ID size is too large (> 7-digits) [-0.5]' );
//	 }
}
?>

