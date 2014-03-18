--------------------------------------------------------------------------------
--
-- SQL schema definition of the Southern Technical Institute of Natural Knowledge
-- student records database, as specified in INFO 214 Assignment 1.
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
  Table_Names Table_Names_T := Table_Names_T ( 'STAFF', 'SERVICE', 'OTHER', 'SALES', 'CUSTOMER', 'CAR', 'FEATURE', 'CAR_FEATURE', 'WARRANTY', 'PURCHASE', 'SALE' ) ;
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
-- CREATE SEQUENCE Staff_ID_Seq START WITH 1000 MAXVALUE 9999;

CREATE TABLE Staff
( Staff_ID      NUMBER(4),
  Firstname     VARCHAR2(50)    NOT NULL,
  Lastname      VARCHAR2(50)    NOT NULL,
  Address       VARCHAR2(150)   NOT NULL,
  -- Use VARCHAR2 for phone numbers in order to retain leading zeros.
  -- Format: leading 0 plus 1-digit area code plus up to 9 digits, e.g.,
  -- 02187654321; longer if we allow punctuation to be included.
  -- (http://en.wikipedia.org/wiki/Telephone_numbers_in_New_Zealand)
  Phone         VARCHAR2(11)    NOT NULL,
  Date_Hired    DATE            DEFAULT SYSDATE
                                NOT NULL,
  Date_of_Birth DATE            NOT NULL,
  --
  CONSTRAINT Staff_Valid_Age CHECK ( ( Date_Hired - TO_YMINTERVAL( '18-0' ) ) >= Date_of_Birth ),
  --
  CONSTRAINT Staff_PK PRIMARY KEY ( Staff_ID )
);


--------------------------------------------------------------------------------
--
-- Service table
--
CREATE TABLE Service
( Staff_ID      NUMBER(4),
  Hourly_Rate   NUMBER(5,2)             NOT NULL    CONSTRAINT Service_Hourly_Rate_Min      CHECK ( Hourly_Rate >= 13.50 ),
  Total_Hours   NUMBER(6,2) DEFAULT 0   NOT NULL    CONSTRAINT Service_Total_Hours_Range    CHECK ( Total_Hours BETWEEN 0 AND 4500 )
                                                    CONSTRAINT Service_Total_Hours_Quarters CHECK ( TRUNC( Total_Hours * 4 ) =  ( Total_Hours * 4 ) ),
  --
  CONSTRAINT Service_PK PRIMARY KEY ( Staff_ID ),
  CONSTRAINT Service_FK_to_Staff FOREIGN KEY ( Staff_ID ) REFERENCES Staff ON DELETE CASCADE
);


--------------------------------------------------------------------------------
--
-- Other table
--
CREATE TABLE Other
( Staff_ID      NUMBER(4),
  Salary        NUMBER(8,2)     NOT NULL    CONSTRAINT Other_Salary_Min CHECK ( Salary >= 28080 ),
  --
  CONSTRAINT Other_PK PRIMARY KEY ( Staff_ID ),
  --
  CONSTRAINT Other_FK_to_Staff FOREIGN KEY ( Staff_ID ) REFERENCES Staff ON DELETE CASCADE
);


--------------------------------------------------------------------------------
--
-- Sales table
--
CREATE TABLE Sales
( Staff_ID          NUMBER(4),
  On_Commission     CHAR(1)     DEFAULT 'N' NOT NULL    CONSTRAINT Sales_Valid_On_Commission    CHECK ( On_Commission IN ( 'Y', 'N' ) ),
  Commission_Rate   NUMBER(3,2)             NOT NULL    CONSTRAINT Sales_Valid_Commission_Rate  CHECK ( Commission_Rate BETWEEN 0.00 AND 0.30 ),
  -- We can't make Gross_Earnings a computed column, because it requires data
  -- from other tables.
  Gross_Earnings    NUMBER(8,2)             NOT NULL    CONSTRAINT Sales_Valid_Gross_Earnings   CHECK ( Gross_Earnings >= 0.00 ),
  --
  CONSTRAINT Sales_Check_Commission CHECK (    ( ( On_Commission = 'N' ) AND ( Commission_Rate = 0 ) )
                                            OR ( ( On_Commission = 'Y' ) AND ( Commission_Rate > 0 ) ) ),
  --
  CONSTRAINT Sales_PK PRIMARY KEY ( Staff_ID ),
  --
  CONSTRAINT Sales_FK_to_Staff FOREIGN KEY ( Staff_ID ) REFERENCES Staff ON DELETE CASCADE
);


--------------------------------------------------------------------------------
--
-- Customer table
--
-- CREATE SEQUENCE Customer_ID_Seq START WITH 100000 MAXVALUE 999999;

CREATE TABLE Customer
( Customer_ID   NUMBER(6),
  Firstname     VARCHAR2(50)    NOT NULL,
  Lastname      VARCHAR2(50)    NOT NULL,
  Address       VARCHAR2(150)   NOT NULL,
  Phone         VARCHAR2(11)    NOT NULL,
  Email         VARCHAR2(50),               -- optionally CHECK format if desired
  Credit_Rating CHAR(1)                     CONSTRAINT Customer_Valid_Credit_Rating CHECK ( Credit_Rating IN ( 'A', 'B', 'C', 'D' ) ),
  Comments      CLOB,
  --
  CONSTRAINT Customer_PK PRIMARY KEY ( Customer_ID )
);


--------------------------------------------------------------------------------
--
-- Car table
--
CREATE TABLE Car
( VIN               CHAR(17),                   -- optionally CHECK format if desired
  Registration      VARCHAR2(6)     NOT NULL,
  Make              VARCHAR2(20)    NOT NULL,
  Model             VARCHAR2(30)    NOT NULL,
  Year              NUMBER(4)       NOT NULL    CONSTRAINT Car_Valid_Year       CHECK ( Year >= 1995 ),
  Colour            VARCHAR2(20)    NOT NULL,
  Odometer          NUMBER(7,1)     NOT NULL    CONSTRAINT Car_Valid_Odometer   CHECK ( Odometer BETWEEN 0.0 AND 999999.9 ),
  First_Registered  DATE            NOT NULL,
  Last_Serviced     DATE,
  Price             NUMBER(6)       NOT NULL    CONSTRAINT Car_Valid_Price      CHECK ( Price >= 0 ),
  Flat_Rate         NUMBER(4)       NOT NULL    CONSTRAINT Car_Valid_Flat_Rate  CHECK ( Flat_Rate > 0 ),
  --
  -- Not specified, but makes sense. Bonus marks!
  CONSTRAINT Car_Valid_Service_Date CHECK ( Last_Serviced >= First_Registered ),
  --
  CONSTRAINT Car_PK PRIMARY KEY ( VIN )
);


--------------------------------------------------------------------------------
--
-- Feature table
--
CREATE TABLE Feature
( Feature_Code  CHAR(5),
  Description   VARCHAR2(100)   NOT NULL,
  --
  CONSTRAINT Feature_PK PRIMARY KEY ( Feature_Code )
);


--------------------------------------------------------------------------------
--
-- Car_Feature table
--
CREATE TABLE Car_Feature
( VIN           CHAR(17),
  Feature_Code  CHAR(5),
  --
  CONSTRAINT Car_Feature_PK PRIMARY KEY ( VIN, Feature_Code ),
  --
  CONSTRAINT Car_Feature_FK_to_Car      FOREIGN KEY ( VIN )          REFERENCES Car     ON DELETE CASCADE,
  CONSTRAINT Car_Feature_FK_to_Feature  FOREIGN KEY ( Feature_Code ) REFERENCES Feature ON DELETE CASCADE
);


--------------------------------------------------------------------------------
--
-- Warranty table
--
CREATE TABLE Warranty
( W_Code        CHAR(1),
  Max_Age       NUMBER(1),
  Max_KM        NUMBER(6),
  Duration      NUMBER(1),
  Distance      NUMBER(4),
  Notes         VARCHAR2(250),
  --
  CONSTRAINT Warranty_PK PRIMARY KEY ( W_Code )
);

-- INSERT INTO Warranty ( W_Code, Max_Age, Max_KM, Duration, Distance, Description ) VALUES ( 'A', 4,    50000,  3, 5000, 'Category A motor vehicle' );
-- INSERT INTO Warranty ( W_Code, Max_Age, Max_KM, Duration, Distance, Description ) VALUES ( 'B', 6,    75000,  2, 3000, 'Category B motor vehicle' );
-- INSERT INTO Warranty ( W_Code, Max_Age, Max_KM, Duration, Distance, Description ) VALUES ( 'C', 8,    100000, 1, 1500, 'Category C motor vehicle' );
-- INSERT INTO Warranty ( W_Code, Max_Age, Max_KM, Duration, Distance, Description ) VALUES ( 'D', NULL, NULL,   0, 0,    'Category D motor vehicle' );


--------------------------------------------------------------------------------
--
-- Purchase table
--
-- CREATE SEQUENCE Purchase_ID_Seq START WITH 10000000 MAXVALUE 99999999;

CREATE TABLE Purchase
( Purchase_ID   NUMBER(8),
  Purchase_Date DATE            NOT NULL,
  Details       CLOB            NOT NULL,
  Amount        NUMBER(6)       NOT NULL    CONSTRAINT Purchase_Valid_Amount CHECK ( Amount >= 0 ),
  VIN           CHAR(17)        NOT NULL,
  Customer_ID   NUMBER(6)       NOT NULL,
  Salesrep_ID   NUMBER(4)       NOT NULL,
  --
  CONSTRAINT Purchase_PK PRIMARY KEY ( Purchase_ID ),
  --
  CONSTRAINT Purchase_FK_to_Car      FOREIGN KEY ( VIN )         REFERENCES Car,
  CONSTRAINT Purchase_FK_to_Customer FOREIGN KEY ( Customer_ID ) REFERENCES Customer,
  CONSTRAINT Purchase_FK_to_Sales    FOREIGN KEY ( Salesrep_ID ) REFERENCES Sales
);


--------------------------------------------------------------------------------
--
-- Sale table
--
-- CREATE SEQUENCE Sale_ID_Seq START WITH 10000000 MAXVALUE 99999999;

CREATE TABLE Sale
( Sale_ID     NUMBER(8),
  Sale_Date   DATE          NOT NULL,
  Details     CLOB          NOT NULL,
  Amount      NUMBER(6)     NOT NULL    CONSTRAINT Sale_Valid_Amount CHECK ( Amount >= 0 ),
  VIN         CHAR(17)      NOT NULL,
  Customer_ID NUMBER(6)     NOT NULL,
  Salesrep_ID NUMBER(4)     NOT NULL,
  W_Code      CHAR(1)       NOT NULL,
  Tradein_ID  NUMBER(8),
  --
  CONSTRAINT Sale_PK PRIMARY KEY ( Sale_ID ),
  --
  CONSTRAINT Sale_FK_to_Car      FOREIGN KEY ( VIN )         REFERENCES Car,
  CONSTRAINT Sale_FK_to_Customer FOREIGN KEY ( Customer_ID ) REFERENCES Customer,
  CONSTRAINT Sale_FK_to_Warranty FOREIGN KEY ( W_Code )      REFERENCES Warranty,
  CONSTRAINT Sale_FK_to_Sales    FOREIGN KEY ( Salesrep_ID ) REFERENCES Sales,
  CONSTRAINT Sale_FK_to_Purchase FOREIGN KEY ( Tradein_ID )  REFERENCES Purchase
);


--------------------------------------------------------------------------------
--
-- Here endeth the schema.
--
--------------------------------------------------------------------------------
