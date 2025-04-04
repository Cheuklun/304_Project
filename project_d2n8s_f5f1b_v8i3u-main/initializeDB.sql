-- DROP EXISTING OBJECTS
DROP TABLE Company CASCADE CONSTRAINTS;
DROP TABLE Individual CASCADE CONSTRAINTS;
DROP TABLE Owner CASCADE CONSTRAINTS;
DROP TABLE Commercial CASCADE CONSTRAINTS;
DROP TABLE Residential CASCADE CONSTRAINTS;
DROP TABLE RentalAgreement CASCADE CONSTRAINTS;
DROP TABLE Tenant CASCADE CONSTRAINTS;
DROP TABLE FinancialTransaction CASCADE CONSTRAINTS;
DROP TABLE Amenity CASCADE CONSTRAINTS;
DROP TABLE AmenityType CASCADE CONSTRAINTS;
DROP TABLE Property CASCADE CONSTRAINTS;
DROP TABLE MaintenanceRequest CASCADE CONSTRAINTS;
DROP TABLE MaintenanceRequestStatus CASCADE CONSTRAINTS;
DROP TABLE PropertyManager CASCADE CONSTRAINTS;
DROP SEQUENCE propertyID_seq;
COMMIT;

------------------------------------------------------------
-- CREATE TABLES (in an order that minimizes dependency issues)
------------------------------------------------------------
-- 1. Tables with no dependencies
CREATE TABLE PropertyManager (
                                 propertyMgrID INT PRIMARY KEY,
                                 propertyMgrName VARCHAR(50) NOT NULL,
                                 phoneNumber VARCHAR(15) NOT NULL,
                                 email VARCHAR(50) NOT NULL
);

CREATE TABLE MaintenanceRequestStatus (
                                          requestID INT PRIMARY KEY,
                                          requestPriority INT NOT NULL,
                                          requestStatus VARCHAR(50) NOT NULL
);

-- 2. Owner table (foreign keys will be added later via update)
CREATE TABLE Owner (
                       ownerID INT PRIMARY KEY,
                       ownershipPercentage FLOAT NOT NULL,
                       ownerName VARCHAR(50) NOT NULL,
                       tenantID INT NULL,         -- Initially NULL
                       contractID INT NULL        -- Initially NULL
    -- Foreign key constraints will be added later via ALTER TABLE if desired.
);

-- 3. Property (depends on PropertyManager and Owner)
CREATE TABLE Property (
                          propertyID INT PRIMARY KEY,
                          propertySize INT NOT NULL,
                          currentValue FLOAT NOT NULL,
                          purchasePrice FLOAT NOT NULL,
                          propertyAddress VARCHAR(100) NOT NULL,
                          propertyMgrID INT NULL,
                          ownerID INT NULL,
                          FOREIGN KEY (propertyMgrID) REFERENCES PropertyManager(propertyMgrID) ON DELETE SET NULL,
                          FOREIGN KEY (ownerID) REFERENCES Owner(ownerID) ON DELETE SET NULL
);

CREATE SEQUENCE propertyID_seq
    START WITH 10
    INCREMENT BY 1
    NOCACHE;


-- 4. Tenant (depends on Property)
CREATE TABLE Tenant (
                        tenantID INT PRIMARY KEY,
                        tenantName VARCHAR(50) NOT NULL,
                        tenantType VARCHAR(50) NOT NULL,
                        propertyID INT NOT NULL,
                        FOREIGN KEY (propertyID) REFERENCES Property(propertyID) ON DELETE CASCADE
);

-- 5. RentalAgreement (depends on Tenant)
CREATE TABLE RentalAgreement (
                                 contractID INT PRIMARY KEY,
                                 monthlyRent FLOAT NOT NULL,
                                 securityDeposit FLOAT NOT NULL,
                                 tenantID INT NOT NULL,
                                 FOREIGN KEY (tenantID) REFERENCES Tenant(tenantID) ON DELETE CASCADE
);

-- 6. Individual (depends on Owner)
CREATE TABLE Individual (
                            ownerID INT PRIMARY KEY,
                            socialInsuranceNum VARCHAR(11) NOT NULL,
                            creditScore INT NOT NULL UNIQUE,
                            FOREIGN KEY (ownerID) REFERENCES Owner(ownerID) ON DELETE CASCADE
);

-- 7. Company (depends on Owner)
CREATE TABLE Company (
                         ownerID INT PRIMARY KEY,
                         businessNum INT NOT NULL,
                         businessType VARCHAR(50) NOT NULL,
                         FOREIGN KEY (ownerID) REFERENCES Owner(ownerID) ON DELETE CASCADE
);

-- 8. Residential (depends on Property)
CREATE TABLE Residential (
                             propertyID INT PRIMARY KEY,
                             resPropertyType VARCHAR(50) NOT NULL,
                             FOREIGN KEY (propertyID) REFERENCES Property(propertyID) ON DELETE CASCADE
);

-- 9. Commercial (depends on Property)
CREATE TABLE Commercial (
                            propertyID INT PRIMARY KEY,
                            numOfUnits INT NOT NULL,
                            commPropertyType VARCHAR(50) NOT NULL,
                            FOREIGN KEY (propertyID) REFERENCES Property(propertyID) ON DELETE CASCADE
);

-- 10. AmenityType (composite primary key)
CREATE TABLE AmenityType (
                             propertyID INT NOT NULL,
                             amenityType VARCHAR(50) NOT NULL,
                             amenitySize INT NOT NULL,
                             PRIMARY KEY (propertyID, amenityType, amenitySize)
);

-- 11. Amenity (depends on AmenityType)
CREATE TABLE Amenity (
                         amenityID INT PRIMARY KEY,
                         propertyID INT NOT NULL,
                         amenityType VARCHAR(50) NOT NULL,
                         amenitySize INT NOT NULL,
                         FOREIGN KEY (propertyID, amenityType, amenitySize)
                             REFERENCES AmenityType(propertyID, amenityType, amenitySize) ON DELETE CASCADE
);

-- 12. MaintenanceRequest (depends on Tenant, PropertyManager, Property)
CREATE TABLE MaintenanceRequest (
                                    requestID INT PRIMARY KEY,
                                    tenantID INT NOT NULL,
                                    propertyMgrID INT NULL,
                                    propertyID INT NOT NULL,
                                    FOREIGN KEY (requestID) REFERENCES MaintenanceRequestStatus(requestID) ON DELETE CASCADE,
                                    FOREIGN KEY (tenantID) REFERENCES Tenant(tenantID) ON DELETE CASCADE,
                                    FOREIGN KEY (propertyMgrID) REFERENCES PropertyManager(propertyMgrID) ON DELETE SET NULL,
                                    FOREIGN KEY (propertyID) REFERENCES Property(propertyID) ON DELETE CASCADE
);

-- 13. FinancialTransaction (depends on Property)
CREATE TABLE FinancialTransaction (
                                      transactionID INT PRIMARY KEY,
                                      transactionDate DATE NOT NULL,
                                      transactionType VARCHAR(50) NOT NULL,
                                      transactionAmount FLOAT NOT NULL,
                                      propertyID INT,
                                      FOREIGN KEY (propertyID) REFERENCES Property(propertyID) ON DELETE CASCADE
);

------------------------------------------------------------
-- INSERT STATEMENTS (Split Insertion Process)
------------------------------------------------------------
-- Step 1: Insert into tables that do not depend on the circular references.

-- PropertyManager
INSERT INTO PropertyManager VALUES (1, 'Alice Manager', '555-1111', 'alice.manager@example.com');
INSERT INTO PropertyManager VALUES (2, 'Bob Manager', '555-2222', 'bob.manager@example.com');
INSERT INTO PropertyManager VALUES (3, 'Carol Manager', '555-3333', 'carol.manager@example.com');
INSERT INTO PropertyManager VALUES (4, 'Dave Manager', '555-4444', 'dave.manager@example.com');
INSERT INTO PropertyManager VALUES (5, 'Eve Manager', '555-5555', 'eve.manager@example.com');

-- MaintenanceRequestStatus
INSERT INTO MaintenanceRequestStatus VALUES (1, 1, 'Open');
INSERT INTO MaintenanceRequestStatus VALUES (2, 2, 'Closed');
INSERT INTO MaintenanceRequestStatus VALUES (3, 1, 'In Progress');
INSERT INTO MaintenanceRequestStatus VALUES (4, 3, 'Open');
INSERT INTO MaintenanceRequestStatus VALUES (5, 2, 'Closed');


-- Step 2: Insert into Owner with tenantID and contractID as NULL.
INSERT INTO Owner VALUES (1, 100.0, 'John Owner', NULL, NULL);
INSERT INTO Owner VALUES (2, 50.0, 'Mary Owner', NULL, NULL);
INSERT INTO Owner VALUES (3, 75.0, 'Steve Owner', NULL, NULL);
INSERT INTO Owner VALUES (4, 60.0, 'Lucy Owner', NULL, NULL);
INSERT INTO Owner VALUES (5, 80.0, 'Mark Owner', NULL, NULL);
INSERT INTO Owner VALUES (11, 100.0, 'Alpha Corp', NULL, NULL);
INSERT INTO Owner VALUES (12, 80.0, 'Beta Corp', NULL, NULL);
INSERT INTO Owner VALUES (13, 90.0, 'Charlie Inc', NULL, NULL);
INSERT INTO Owner VALUES (14, 70.0, 'Delta Ltd', NULL, NULL);
INSERT INTO Owner VALUES (15, 85.0, 'Epsilon LLC', NULL, NULL);

-- Step 3: Insert into Property (depends on PropertyManager and Owner).
INSERT INTO Property VALUES (1, 1500, 300000.0, 250000.0, '123 Maple St', 1, 1);
INSERT INTO Property VALUES (2, 2000, 450000.0, 400000.0, '456 Oak Ave', 2, 2);
INSERT INTO Property VALUES (3, 1800, 350000.0, 300000.0, '789 Pine Rd', 3, 3);
INSERT INTO Property VALUES (4, 2200, 500000.0, 450000.0, '101 Cedar Blvd', 4, 4);
INSERT INTO Property VALUES (5, 1600, 320000.0, 280000.0, '202 Birch Ln', 5, 5);
INSERT INTO Property VALUES (6, 1400, 280000.0, 230000.0, '303 Spruce St', 1, 1);
INSERT INTO Property VALUES (7, 1300, 260000.0, 210000.0, '404 Elm St', 2, 2);
INSERT INTO Property VALUES (8, 2500, 600000.0, 550000.0, '505 Walnut Ave', 3, 3);
INSERT INTO Property VALUES (9, 2600, 620000.0, 570000.0, '606 Cherry St', 4, 4);
INSERT INTO Property VALUES (10, 2400, 580000.0, 530000.0, '707 Poplar Rd', 5, 5);

-- Step 4: Insert into Tenant (depends on Property).
INSERT INTO Tenant VALUES (1, 'Tenant One', 'Residential', 1);
INSERT INTO Tenant VALUES (2, 'Tenant Two', 'Commercial', 2);
INSERT INTO Tenant VALUES (3, 'Tenant Three', 'Residential', 3);
INSERT INTO Tenant VALUES (4, 'Tenant Four', 'Commercial', 4);
INSERT INTO Tenant VALUES (5, 'Tenant Five', 'Residential', 5);

-- Step 5: Insert into RentalAgreement (depends on Tenant).
INSERT INTO RentalAgreement VALUES (1, 1500.0, 1500.0, 1);
INSERT INTO RentalAgreement VALUES (2, 2000.0, 2000.0, 2);
INSERT INTO RentalAgreement VALUES (3, 1800.0, 1800.0, 3);
INSERT INTO RentalAgreement VALUES (4, 2200.0, 2200.0, 4);
INSERT INTO RentalAgreement VALUES (5, 1600.0, 1600.0, 5);

-- Step 6: Now update Owner to set the proper tenantID and contractID.
UPDATE Owner SET tenantID = 1, contractID = 1 WHERE ownerID = 1;
UPDATE Owner SET tenantID = 2, contractID = 2 WHERE ownerID = 2;
UPDATE Owner SET tenantID = 3, contractID = 3 WHERE ownerID = 3;
UPDATE Owner SET tenantID = 4, contractID = 4 WHERE ownerID = 4;
UPDATE Owner SET tenantID = 5, contractID = 5 WHERE ownerID = 5;
UPDATE Owner SET tenantID = 1, contractID = 1 WHERE ownerID = 11;
UPDATE Owner SET tenantID = 2, contractID = 2 WHERE ownerID = 12;
UPDATE Owner SET tenantID = 3, contractID = 3 WHERE ownerID = 13;
UPDATE Owner SET tenantID = 4, contractID = 4 WHERE ownerID = 14;
UPDATE Owner SET tenantID = 5, contractID = 5 WHERE ownerID = 15;

-- Step 7: Insert into Individual (depends on Owner).
INSERT INTO Individual VALUES (1, '123 456 789', 750);
INSERT INTO Individual VALUES (2, '987 654 321', 720);
INSERT INTO Individual VALUES (3, '192 837 465', 680);
INSERT INTO Individual VALUES (4, '564 738 291', 700);
INSERT INTO Individual VALUES (5, '102 938 475', 710);

-- Step 8: Insert into Company (depends on Owner).
INSERT INTO Company VALUES (11, 111111, 'Real Estate');
INSERT INTO Company VALUES (12, 222222, 'Property Investment');
INSERT INTO Company VALUES (13, 333333, 'Realty');
INSERT INTO Company VALUES (14, 444444, 'Asset Management');
INSERT INTO Company VALUES (15, 555555, 'Property Services');

-- Step 9: Insert into Residential (depends on Property).
INSERT INTO Residential VALUES (1, 'Apartment');
INSERT INTO Residential VALUES (3, 'Condo');
INSERT INTO Residential VALUES (5, 'House');
INSERT INTO Residential VALUES (6, 'Townhouse');
INSERT INTO Residential VALUES (7, 'Loft');

-- Step 10: Insert into Commercial (depends on Property).
INSERT INTO Commercial VALUES (2, 10, 'Office');
INSERT INTO Commercial VALUES (4, 15, 'Retail');
INSERT INTO Commercial VALUES (8, 8, 'Office');
INSERT INTO Commercial VALUES (9, 12, 'Retail');
INSERT INTO Commercial VALUES (10, 9, 'Warehouse');

-- Step 11: Insert into AmenityType.
INSERT INTO AmenityType VALUES (1, 'Pool', 500);
INSERT INTO AmenityType VALUES (2, 'Gym', 1200);
INSERT INTO AmenityType VALUES (3, 'Parking', 3000);
INSERT INTO AmenityType VALUES (4, 'Garden', 430);
INSERT INTO AmenityType VALUES (5, 'Elevator', 50);
INSERT INTO AmenityType VALUES (6, 'Pool', 600);

-- Step 12: Insert into Amenity.
INSERT INTO Amenity VALUES (1, 1, 'Pool', 500);
INSERT INTO Amenity VALUES (2, 2, 'Gym', 1200);
INSERT INTO Amenity VALUES (3, 3, 'Parking', 3000);
INSERT INTO Amenity VALUES (4, 4, 'Garden', 430);
INSERT INTO Amenity VALUES (5, 5, 'Elevator', 50);

-- Step 13: Insert into MaintenanceRequest.
INSERT INTO MaintenanceRequest VALUES (1, 1, 1, 1);
INSERT INTO MaintenanceRequest VALUES (2, 2, 2, 2);
INSERT INTO MaintenanceRequest VALUES (3, 3, 3, 3);
INSERT INTO MaintenanceRequest VALUES (4, 4, 4, 4);
INSERT INTO MaintenanceRequest VALUES (5, 5, 5, 5);

-- Step 14: Insert into FinancialTransaction.
INSERT INTO FinancialTransaction VALUES (1, TO_DATE('2024-01-01', 'YYYY-MM-DD'), 'Rent Payment', 1500.00, 1);
INSERT INTO FinancialTransaction VALUES (2, TO_DATE('2024-02-01', 'YYYY-MM-DD'), 'Rent Payment', 1500.00, 1);
INSERT INTO FinancialTransaction VALUES (3, TO_DATE('2024-01-10', 'YYYY-MM-DD'), 'Maintenance Fee', 300.00, 2);
INSERT INTO FinancialTransaction VALUES (4, TO_DATE('2024-02-15', 'YYYY-MM-DD'), 'Security Deposit Refund', 1800.00, 3);
INSERT INTO FinancialTransaction VALUES (5, TO_DATE('2024-03-01', 'YYYY-MM-DD'), 'Rent Payment', 1600.00, 5);

COMMIT;