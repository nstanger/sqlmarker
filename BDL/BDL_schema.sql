--------------------------------------------------------------------------------
--
-- SQL schema definition of the Better Digital, Ltd. (BDL) corporate
-- database, as specified in INFO 214 Assignment 1.
--
-- Schema written for Oracle11g.
--
--------------------------------------------------------------------------------
--
-- NOTE: this script will drop any existing tables automatically so
-- that they can be re-created.
--


DECLARE
  TYPE Table_Names_T IS TABLE OF VARCHAR2 ( 32 ) ;
  Table_Names Table_Names_T := Table_Names_T ( 'ASSEMBLY', 'COMPONENT', 'CUSTOMER', 'ORDER_HEAD', 'ORDER_LINE', 'PRODUCT', 'SALE_HEAD', 'SALE_LINE', 'STAFF', 'SUPPLIER' ) ;
  Table_Nonexistent EXCEPTION;
  PRAGMA Exception_Init ( Table_Nonexistent, -942 ) ;
BEGIN
  
  FOR T IN Table_Names.FIRST..Table_Names.LAST
  LOOP
    BEGIN
      EXECUTE IMMEDIATE 'DROP TABLE ' || Table_Names ( T ) || '' || ' CASCADE CONSTRAINTS';
    
    EXCEPTION
      -- We only want to suppress exceptions due to the table not existing.  That's
      -- -942: ORA-00942: table or view does not exist
      -- Other exceptions, such as being unable to drop a table because of locks(!), should not be trapped, so that the user sees an error message.
    
    WHEN Table_Nonexistent THEN
      DBMS_Output.Put_Line ( SQLCODE || ': ' || SQLERRM || ': Ignoring nonexistent table ' || T ) ;
      -- null;
    
    END;
  
  END LOOP;

END;
/




--------------------------------------------------------------------------------
--
-- Staff table
--
CREATE TABLE Staff
( Staff_ID    NUMBER(7),
  --
  Surname     VARCHAR2(50)  NOT NULL,
  --
  Firstnames  VARCHAR2(50)  NOT NULL,
  --
  Phone       VARCHAR2(15)  NOT NULL,
  --
  Address     VARCHAR2(150) NOT NULL,
  --
  Department  VARCHAR2(18)  NOT NULL
    CONSTRAINT Staff_Department_Invalid
      -- Yes, this should really be a lookup table :)
      CHECK ( Department IN ( 'Central Management',
                              'Sales ' || chr(38) || ' Marketing',
                              'Personnel',
                              'Manufacturing',
                              'Inventory',
                              'Accounts' ) ),
  --
  Position    VARCHAR2(20)  NOT NULL
    CONSTRAINT Staff_POSITION_INVALID
      -- Yes, this should really be a lookup table too :)
      CHECK ( Position IN ( 'CEO', 'CTO', 'CFO', 'CIO', 'Director',
                            'President', 'Vice-President', 'Manager',
                            'Personal Assistant', 'Secretary',
                            'Technician', 'Researcher', 'Designer',
                            'Assembler', 'Programmer', 'Contractor',
                            'Sales Representative', 'Accountant',
                            'Inventory', 'Assistant' ) ),
  --
  Salary      NUMBER(7,2)   NOT NULL
    CONSTRAINT Staff_Salary_Range_Invalid CHECK ( Salary BETWEEN 1000 and 99999.99 ),
  --
  Comments    VARCHAR2(4000),
  --
  CONSTRAINT STAFF_PK PRIMARY KEY ( Staff_ID )
);


--------------------------------------------------------------------------------
--
-- Customer table
--
CREATE TABLE Customer
( Customer_ID     NUMBER(7),
  --
  Name            VARCHAR2(50)    NOT NULL,
  --
  Contact_Person  VARCHAR2(50),
  --
  Phone           VARCHAR2(15)    NOT NULL,
  --
  Address         VARCHAR2(200)   NOT NULL,
  --
  Email           VARCHAR2(50),
  --
  Comments        VARCHAR2(4000),
  --
  CONSTRAINT Customer_PK PRIMARY KEY ( Customer_ID )
);


--------------------------------------------------------------------------------
--
-- Supplier table
--
CREATE TABLE Supplier
( Supplier_ID     NUMBER(7),
  --
  Name            VARCHAR2(50)    NOT NULL,
  --
  Contact_Person  VARCHAR2(50),
  --
  Phone           VARCHAR2(15)    NOT NULL,
  --
  Address         VARCHAR2(200)   NOT NULL,
  --
  Email           VARCHAR2(50),
  --
  Comments        VARCHAR2(4000),
  --
  CONSTRAINT Supplier_PK PRIMARY KEY ( Supplier_ID )
);


--------------------------------------------------------------------------------
--
-- Product table
--
CREATE TABLE Product
( Product_Code      NUMBER(8),
  --
  Description       VARCHAR2(50)  NOT NULL,
  --
  Stock_Count       NUMBER(5)     NOT NULL
    CONSTRAINT Product_Stock_Count_Too_Low CHECK ( Stock_Count BETWEEN 0 AND 99999 ),
  --
  Restock_Level     NUMBER(5)
    CONSTRAINT Product_Restock_Level_Too_Low CHECK ( Restock_Level BETWEEN 0 AND 99999 ),
  --
  Minimum_Level     NUMBER(5),
  --
  List_Price        NUMBER(7,2)   NOT NULL
    CONSTRAINT Product_List_Price_Too_Low CHECK ( List_Price BETWEEN 0 AND 99999.99 ),
  --
  Assembly_Manual   BLOB,
  --
  Assembly_Program  BLOB,
  --
  CONSTRAINT Product_Min_Level_Invalid CHECK ( ( Minimum_Level >= 0 ) AND ( Minimum_Level < Restock_Level ) ),
  --
  CONSTRAINT Product_PK PRIMARY KEY ( Product_Code )
);


--------------------------------------------------------------------------------
--
-- Component table
--
CREATE TABLE Component
( Component_Code  NUMBER(8),
  --
  Suppliers_Code  VARCHAR2(25)  NOT NULL,
  --
  Description     VARCHAR2(100) NOT NULL,
  --
  Stock_Count     NUMBER(7)     NOT NULL
    CONSTRAINT Component_Stk_Count_Too_Low CHECK ( Stock_Count BETWEEN 0 AND 9999999 ),
  --
  Supplier_ID     NUMBER(7)     NOT NULL,
  --
  CONSTRAINT Component_PK PRIMARY KEY ( Component_Code ),
  --
  CONSTRAINT Component_FK_To_Supplier FOREIGN KEY ( Supplier_ID ) REFERENCES Supplier
);


--------------------------------------------------------------------------------
--
-- Assembly table
--
CREATE TABLE Assembly
( Product_Code    NUMBER(8),
  --
  Component_Code  NUMBER(8),
  --
  Quantity        NUMBER(4)     NOT NULL
    CONSTRAINT Assembly_Quantity_Too_Low CHECK ( Quantity BETWEEN 1 AND 9999 ),
  --
  CONSTRAINT Assembly_PK PRIMARY KEY ( Product_Code, Component_Code ),
  --
  CONSTRAINT Assembly_FK_To_Product FOREIGN KEY ( Product_Code ) REFERENCES Product,
  --
  CONSTRAINT Assembly_FK_To_Component FOREIGN KEY ( Component_Code ) REFERENCES Component
);


--------------------------------------------------------------------------------
--
-- Sale_Head table
--
CREATE TABLE Sale_Head
( Sale_Num     NUMBER(10),
  --
  Sale_Date    DATE           NOT NULL,
  --
  Date_Entered DATE           DEFAULT SYSDATE NOT NULL,
  --
  Status       VARCHAR2(11)   NOT NULL
    CONSTRAINT Sale_Head_Status_Invalid
      CHECK ( Status IN ( 'pending', 'in progress', 'cancelled', 'backordered', 'shipped' ) ),
  --
  Staff_ID     NUMBER(7)      NOT NULL,
  --
  Customer_ID  NUMBER(7)      NOT NULL,
  --
  Comments     VARCHAR2(4000),
  --
  CONSTRAINT Sale_Head_Date_In_Future CHECK ( Sale_Date <= Date_Entered ),
  --
  CONSTRAINT Sale_Head_PK PRIMARY KEY ( Sale_Num ),
  --
  CONSTRAINT Sale_Head_FK_To_Staff FOREIGN KEY ( Staff_ID ) REFERENCES Staff,
  --
  CONSTRAINT Sale_Head_FK_To_Customer FOREIGN KEY ( Customer_ID ) REFERENCES Customer
);


--------------------------------------------------------------------------------
--
-- Sale_Line table
--
CREATE TABLE Sale_Line
( Sale_Num      NUMBER(10),
  --
  Product_Code  NUMBER(8),
  --
  Quantity      NUMBER(4)   NOT NULL
    CONSTRAINT Sale_Line_Quantity_Too_Low CHECK ( Quantity > 0 ),
  --
  Actual_Price  NUMBER(7,2) NOT NULL
    CONSTRAINT Sale_Line_Act_Price_Too_Low CHECK ( Actual_Price BETWEEN 0 AND 99999.99 ),
  --
  CONSTRAINT Sale_Line_PK PRIMARY KEY ( Sale_Num, Product_Code ),
  --
  CONSTRAINT Sale_Line_FK_To_Product FOREIGN KEY ( Product_Code ) REFERENCES Product,
  --
  CONSTRAINT Sale_Line_FK_To_Sale_Hd FOREIGN KEY ( Sale_Num ) REFERENCES Sale_Head
);


--------------------------------------------------------------------------------
--
-- Order_Head table
--
CREATE TABLE Order_Head
( Order_Num      NUMBER(10),
  --
  Order_Date     DATE           NOT NULL,
  --
  Date_Entered   DATE           DEFAULT SYSDATE NOT NULL,
  --
  Due_Date       DATE           NOT NULL,
  --
  Date_Completed DATE,
  --
  Status         VARCHAR2(11)   NOT NULL
    CONSTRAINT Order_Head_Status_Invalid CHECK ( Status IN ( 'complete', 'in progress' ) ),
  --
  Staff_ID       NUMBER(7)      NOT NULL,
  --
  Supplier_ID    NUMBER(7)      NOT NULL,
  --
  Comments       VARCHAR2(4000),
  --
  CONSTRAINT Order_Head_Date_In_Future CHECK ( Order_Date <= Date_Entered ),
  --
  CONSTRAINT Order_Head_Due_Date_Invalid CHECK ( Due_Date > Order_Date ),
  --
  CONSTRAINT Order_Head_Date_Comp_Invalid CHECK ( Date_Completed > Order_Date ),
  --
  CONSTRAINT Order_Head_PK PRIMARY KEY ( Order_Num ),
  --
  CONSTRAINT Order_Head_FK_To_Staff FOREIGN KEY ( Staff_ID ) REFERENCES Staff,
  --
  CONSTRAINT Order_Head_FK_To_Supp FOREIGN KEY ( Supplier_ID ) REFERENCES Supplier
);


--------------------------------------------------------------------------------
--
-- Order_Line table
--
CREATE TABLE Order_Line
( Order_Num       NUMBER(10),
  --
  Component_Code  NUMBER(8),
  --
  Qty_Ordered     NUMBER(5)     NOT NULL
    CONSTRAINT Order_Line_Qty_Ord_Too_Low CHECK ( Qty_Ordered BETWEEN 1 AND 99999 ),
  --
  Price           NUMBER(6,2)   NOT NULL
    CONSTRAINT Order_Line_Price_Too_Low CHECK ( Price BETWEEN 0 AND 9999.99 ),
  --
  Qty_Received    NUMBER(6)     NOT NULL
    CONSTRAINT Order_Line_Qty_Rec_Too_Low CHECK ( Qty_Received >= 0 ),
  --
  CONSTRAINT Order_Line_PK PRIMARY KEY ( Order_Num, Component_Code ),
  --
  CONSTRAINT Order_Line_FK_To_Comp FOREIGN KEY ( Component_Code ) REFERENCES Component,
  --
  CONSTRAINT Order_Line_FK_To_Ord_Hd FOREIGN KEY ( Order_Num ) REFERENCES Order_Head
);


--------------------------------------------------------------------------------
--
-- Here endeth the schema.
--
--------------------------------------------------------------------------------

