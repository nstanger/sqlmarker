<?php
require_once "Schema.php";

class BDL_Test_Staff extends PHPUnit_Extensions_Database_TestCase_CreateTable
{
	public function getTableName()
	{
		return 'STAFF';
	}
	
	
	public function getColumnList()
	{
		return array(
			'STAFF_ID'		=>	array(	'type'			=>	array( 'NUMBER', 'INTEGER' ),
										'min_length'	=>	7,
										'max_length'	=>	7,
										'decimals'		=>	0,
// 										'underflow'		=>	-1,
										'nullable'		=>	false,
										'test_value'	=>	"123456",	),
			'SURNAME'		=>	array(	'type'			=>	array( 'VARCHAR2', 'VARCHAR' ),
										'min_length'	=>	50,
										'max_length'	=>	50,
										'nullable'		=>	false,
										'test_value'	=>	"'Bloggs'",	),
			'FIRSTNAMES'	=>	array(	'type'			=>	array( 'VARCHAR2', 'VARCHAR' ),
										'min_length'	=>	50,
										'max_length'	=>	50,
										'nullable'		=>	false,
										'test_value'	=>	"'Harold'",	),
			'PHONE'			=>	array(	'type'			=>	array( 'VARCHAR2', 'CHAR', 'VARCHAR' ),
										'min_length'	=>	11,
										'max_length'	=>	25,
										'nullable'		=>	false,
										'test_value'	=>	"'16139981234'",	),
			'ADDRESS'		=>	array(	'type'			=>	array( 'VARCHAR2', 'VARCHAR' ),
										'min_length'	=>	150,
										'max_length'	=>	150,
										'nullable'		=>	false,
										'test_value'	=>	"'12 Western Way'",	),
			'DEPARTMENT'	=>	array(	'type'			=>	array( 'VARCHAR2', 'VARCHAR' ),
										'min_length'	=>	18,
										'max_length'	=>	20,
										'nullable'		=>	false,
										'legal_values'	=> array(
											"'Central Management'", "'Sales ' || chr(38) || ' Marketing'",
											"'Personnel'", "'Manufacturing'", "'Inventory'", "'Accounts'"	),
										'illegal_values' => array(	"'foobar'", "'blurk'", "'   '"	),
										'test_value'	=>	"'Accounts'",	),
			'POSITION'		=>	array(	'type'			=>	array( 'VARCHAR2', 'VARCHAR' ),
										'min_length'	=>	20,
										'max_length'	=>	20,
										'nullable'		=>	false,
										'legal_values'	=> array(
											"'CEO'", "'CTO'", "'CFO'", "'CIO'", "'Director'", "'President'",
											"'Vice-President'", "'Manager'", "'Personal Assistant'",
											"'Secretary'", "'Technician'", "'Researcher'", "'Designer'",
											"'Assembler'", "'Programmer'", "'Contractor'",
											"'Sales Representative'", "'Accountant'", "'Inventory'",
											"'Assistant'"	),
										'illegal_values' => array(	"'foobar'", "'blurk'", "'   '"	),
										'test_value'	=>	"'CEO'",	),
			'SALARY'		=>	array(	'type'			=>	array( 'NUMBER', 'DECIMAL' ),
										'min_length'	=>	7,
										'max_length'	=>	7,
										'decimals'		=>	2,
										'underflow'		=>	999.99,
										'overflow'		=>	100000.00,
										'nullable'		=>	false,
										'test_value'	=>	"50000.00",	),
			'COMMENTS'		=>	array(	'type'			=>	array( 'VARCHAR2', 'CLOB' ),
										'min_length'	=>	1000,
										'max_length'	=>	4000,
										'nullable'		=>	true,
										'test_value'	=>	"'Blah blah blah'",	),	);
	}
	
	
	public function getPKColumnList()
	{
		return array( 'STAFF_ID' );
	}
	
	
	public function getFKColumnList()
	{
		return array();
	}
	
	
	/**
	 *	Return fixture data set for current database connection.
	 *
	 *	@access protected
	 *	@return PHPUnit_Extensions_Database_DataSet_IDataSet
	 *	@todo Parameterise the fixture filename.
	 */
	protected function getDataSet()
	{
		return $this->createXMLDataSet("BDL_Fixture_Staff.xml");
	}
	
	
	public function testTableExists()
	{
		$this->assertTableExists();
	}
	
	
	/**
	 *	@dataProvider provideColumnNames
	 */
	public function testColumnExists( $columnName )
	{
   		$this->assertColumnExists( $columnName );
	}
	
	
	/**
	 *	@dataProvider provideColumnTypes
	 */
	public function testColumnDataType( $columnName, $columnTypeList )
	{
   		$this->assertColumnDataType( $columnName, $columnTypeList );
	}
	
	
	/**
	 *	@dataProvider provideColumnLengths
	 */
	public function testColumnLength( $columnName, $columnLengthList )
	{
   		$this->assertColumnLength( $columnName, $columnLengthList );
	}
	
	
	/**
	 *	@dataProvider provideColumnLegalValues
	 */
	public function testColumnLegalValue( $columnName, $legalValue )
	{
   		$this->assertColumnLegalValue( $columnName, $legalValue );
	}
	
	
	/**
	 *	@dataProvider provideColumnIllegalValues
	 *	@expectedException PDOException
	 *	@expectedExceptionMessage check constraint
	 *	@expectedExceptionCode HY000
	 */
	public function testColumnIllegalValue( $columnName, $illegalValue )
	{
   		$this->assertColumnIllegalValue( $columnName, $illegalValue );
	}
	
	
	/**
	 *	@dataProvider provideColumnUnderflowValues
	 *	@expectedException PDOException
	 *	@expectedExceptionMessage check constraint
	 *	@expectedExceptionCode HY000
	 */
	public function testColumnUnderflowValue( $columnName, $underflowValue )
	{
   		$this->assertColumnUnderflowValue( $columnName, $underflowValue );
	}
	
	
	/**
	 *	@dataProvider provideColumnOverflowValues
	 *	@expectedException PDOException
	 *	@expectedExceptionMessage check constraint
	 *	@expectedExceptionCode HY000
	 */
	public function testColumnOverflowValue( $columnName, $overflowValue )
	{
   		$this->assertColumnOverflowValue( $columnName, $overflowValue );
	}
	
	
	/**
	 *	@dataProvider provideColumnNullabilities
	 */
	public function testColumnNullability( $columnName, $columnNullability )
	{
   		$this->assertColumnNullability( $columnName, $columnNullability );
	}
	
	
	public function testPKExists()
	{
		return $this->assertPKExists();
	}
	
	
	/**
	 *	@depends testPKExists
	 */
	public function testPKColumns( $constraintName )
	{
		$this->assertPKColumns( $constraintName );
	}
	
	
	/**
	 *	@dataProvider provideConstraintNames
	 */
	public function testConstraintsNamed( $constraintName, $constraintType )
	{
		$this->assertConstraintNamed( $constraintName, $constraintType );
	}
}
?>
