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
  Table_Names Table_Names_T := Table_Names_T ( 'ASSESSMENT', 'ENROLMENT', 'PAPER', 'PERSON', 'QUALIFICATION', 'RESULT', 'SCHEDULE', 'STAFF', 'STUDENT', 'TEACH' ) ;
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
-- Qualification table
--
CREATE TABLE Qualification
( Abbreviation      VARCHAR2(10),
  Full_Name         VARCHAR2(100)   NOT NULL,
  Type              VARCHAR2(11)    NOT NULL
    CONSTRAINT Qualification_Type_Valid 
      CHECK ( Type IN ( 'Degree', 'Diploma', 'Certificate' ) ),
  --
  CONSTRAINT Qualification_PK PRIMARY KEY ( Abbreviation )
);


--------------------------------------------------------------------------------
--
-- Paper table
--
CREATE TABLE Paper
( Paper_Code        CHAR(7),
  Title             VARCHAR2(50)    NOT NULL,
  Description       VARCHAR2(500)   NOT NULL,
  Points            NUMBER(2)       DEFAULT 18 NOT NULL
    CONSTRAINT Paper_Points_Range CHECK ( Points BETWEEN 0 AND 36 ),
  Period            CHAR(2)        NOT NULL
    CONSTRAINT Paper_Period_Valid
      CHECK ( Period IN ( 'S1', 'S2', 'SS', 'FY' ) ),
  --
  CONSTRAINT Paper_PK PRIMARY KEY ( Paper_Code )
);


--------------------------------------------------------------------------------
--
-- Schedule table
--
CREATE TABLE Schedule
( Abbreviation      VARCHAR2(10),
  Paper_Code        CHAR(7),
  --
  CONSTRAINT Schedule_PK PRIMARY KEY ( Abbreviation, Paper_Code ),
  CONSTRAINT Schedule_FK_to_Qualification
    FOREIGN KEY ( Abbreviation ) REFERENCES Qualification,
  CONSTRAINT Schedule_FK_to_Paper FOREIGN KEY ( Paper_Code ) REFERENCES Paper
);


--------------------------------------------------------------------------------
--
-- Person table
--
CREATE TABLE Person
( Person_ID         NUMBER(7),
  Surname           VARCHAR2(50)    NOT NULL,
  Other_Names       VARCHAR2(50)    NOT NULL,
  Contact_Phone     VARCHAR2(11),   -- at least 11, maybe more 
  Contact_Address   VARCHAR2(200)   NOT NULL,
  Email             VARCHAR2(50)    NOT NULL,
  Username          VARCHAR2(50)    NOT NULL
    CONSTRAINT Person_Username_Unique UNIQUE, -- bonus marks!
  --
  CONSTRAINT Person_PK PRIMARY KEY ( Person_ID )
);


--------------------------------------------------------------------------------
--
-- Staff table
--
CREATE TABLE Staff
( Staff_ID          NUMBER(7),
  Rank              VARCHAR2(2)     NOT NULL
    CONSTRAINT Staff_Rank_Valid
      CHECK ( Rank IN ( 'T', 'AL', 'L', 'SL', 'AP', 'P' ) ),
  Salary            NUMBER(8,2)     NOT NULL
    CONSTRAINT Staff_Salary_Range CHECK ( Salary >= 40450 ),
  --
  CONSTRAINT Staff_PK PRIMARY KEY ( Staff_ID ),
  CONSTRAINT Staff_FK_to_Person
    FOREIGN KEY ( Staff_ID ) REFERENCES Person ( Person_ID )
);


--------------------------------------------------------------------------------
--
-- Student table
--
CREATE TABLE Student
( Student_ID        NUMBER(7),
  Home_Phone        VARCHAR2(15),   -- ITU Recommendation E.164
  Home_Address      VARCHAR2(200)   NOT NULL,
  International     CHAR(1)         DEFAULT 'F' NOT NULL
    CONSTRAINT Student_International_Valid
      CHECK ( International IN ( 'T', 'F' ) ),
  Supervisor_ID     NUMBER(7),      -- optional
  --
  CONSTRAINT Student_PK PRIMARY KEY (Student_ID),
  CONSTRAINT Student_FK_to_Person
    FOREIGN KEY (Student_ID) REFERENCES Person (Person_ID),
  CONSTRAINT Student_FK_to_Staff
    FOREIGN KEY (Supervisor_ID) REFERENCES Staff (Staff_ID)
);


--------------------------------------------------------------------------------
--
-- Teach table
--
CREATE TABLE Teach
( Staff_ID          NUMBER(7),
  Paper_Code        CHAR(7),
  Year_Taught       NUMBER(4),
    CONSTRAINT Teach_Year_Taught_Range CHECK ( Year_Taught >= 1982 ),
  Role              VARCHAR2(11)    NOT NULL
    CONSTRAINT Teach_Role_Valid
      CHECK ( Role IN ( 'Coordinator', 'Lecturer', 'Tutor' ) ),
  --
  CONSTRAINT Teach_PK PRIMARY KEY ( Staff_ID, Paper_Code, Year_Taught ),
  CONSTRAINT Teach_FK_to_Staff FOREIGN KEY ( Staff_ID ) REFERENCES Staff,
  CONSTRAINT Teach_FK_to_Paper FOREIGN KEY ( Paper_Code ) REFERENCES Paper
);


--------------------------------------------------------------------------------
--
-- Enrolment table
--
CREATE TABLE Enrolment
( Enrolment_ID      NUMBER(10),
  Description       VARCHAR2(100)   NOT NULL,
  Year_Enrolled     NUMBER(4)       NOT NULL
    CONSTRAINT Enrolment_Year_Enrolled_Range
      CHECK (Year_Enrolled >= 1982),
  Comments          VARCHAR2(4000), -- or CLOB
  Student_ID        NUMBER(7)       NOT NULL,
  Paper_Code        CHAR(7)         NOT NULL,
  --
  CONSTRAINT Enrolment_PK PRIMARY KEY ( Enrolment_ID ),
  CONSTRAINT Enrolment_FK_to_Student
    FOREIGN KEY ( Student_ID ) REFERENCES Student,
  CONSTRAINT Enrolment_FK_to_Paper
    FOREIGN KEY ( Paper_Code ) REFERENCES Paper
);


--------------------------------------------------------------------------------
--
-- Assessment table
--
CREATE TABLE Assessment
( Assessment_ID     NUMBER(10),
  Assessment_Year   NUMBER(4)       NOT NULL
    CONSTRAINT Assessment_Year_Range
      CHECK ( Assessment_Year >= 1982 ),
  Name              VARCHAR2(50)    NOT NULL,
  Description       VARCHAR2(500),
  Type              CHAR(1)         NOT NULL
    CONSTRAINT Assessment_Type_Valid
      CHECK ( Type IN ( 'A', 'P', 'T', 'X' ) ),
  Release           CHAR(1)         DEFAULT 'F' NOT NULL
    CONSTRAINT Assessment_Release_Valid CHECK ( Release IN ( 'T', 'F' ) ),
  Weight            NUMBER(3)       NOT NULL
    CONSTRAINT Assessment_Weight_Range CHECK ( Weight BETWEEN 0 AND 100 ),
  Maximum_Mark      NUMBER(3),
  Paper_Code        CHAR(7)         NOT NULL,
  --
  CONSTRAINT Assessment_PK PRIMARY KEY ( Assessment_ID ),
  CONSTRAINT Assessment_FK_to_Paper
    FOREIGN KEY ( Paper_Code ) REFERENCES Paper
);


--------------------------------------------------------------------------------
--
-- Result table
--
CREATE TABLE Result
( Assessment_ID     NUMBER(10),
  Enrolment_ID      NUMBER(10),
  Raw_Mark          NUMBER(4,1)     NOT NULL,
  Weighted_Mark     NUMBER          NOT NULL,
  Percentage_Mark   NUMBER(5,2)     NOT NULL
    CONSTRAINT Result_Percentage_Mark_Range
      CHECK (Percentage_Mark BETWEEN 0 AND 100),
  --
  CONSTRAINT Result_PK PRIMARY KEY ( Assessment_ID, Enrolment_ID ),
  CONSTRAINT Result_FK_to_Assessment
    FOREIGN KEY ( Assessment_ID ) REFERENCES Assessment,
  CONSTRAINT Result_FK_to_Enrolment
    FOREIGN KEY ( Enrolment_ID ) REFERENCES Enrolment
);


--------------------------------------------------------------------------------
--
-- Here endeth the schema.
--
--------------------------------------------------------------------------------
