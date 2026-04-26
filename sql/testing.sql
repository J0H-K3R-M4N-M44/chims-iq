USE chimsiq_db;

-- ======================
-- TEST 1: Multi-Tenant Isolation
-- ======================
SELECT '=== TEST 1: Multi-Tenant Isolation ===' AS test;
SELECT COUNT(*) AS total_stores FROM STORE;
SELECT s.StoreID, s.StoreName, u.FullName, u.Role 
FROM STORE s 
JOIN USER u ON s.AdminUserID = u.UserID;

-- ======================
-- TEST 2: Stock Health Trigger (Critical Test)
-- ======================
SELECT '=== TEST 2: Stock Health Trigger ===' AS test;

-- Update quantity to test trigger
UPDATE STOCK 
SET Quantity = 12 
WHERE ProductID = (SELECT ProductID FROM PRODUCT WHERE ProductName = 'RTX 4060 8GB' LIMIT 1);

UPDATE STOCK 
SET Quantity = 3 
WHERE ProductID = (SELECT ProductID FROM PRODUCT WHERE ProductName = '16GB DDR4 RAM' LIMIT 1);

UPDATE STOCK 
SET Quantity = 0 
WHERE ProductID = (SELECT ProductID FROM PRODUCT WHERE ProductName = 'RTX 4060 8GB' LIMIT 1);  -- Force Critical

-- Verify trigger worked
SELECT p.ProductName, s.Quantity, s.MinStockLevel, s.HealthStatus, s.LastUpdated 
FROM PRODUCT p 
JOIN STOCK s ON p.ProductID = s.ProductID 
ORDER BY p.ProductName;

-- ======================
-- TEST 3: Soft-Delete + Recovery (DELETION_LOG)
-- ======================
SELECT '=== TEST 3: Soft-Delete ===' AS test;

-- Delete a product (should create log entry)
DELETE FROM PRODUCT 
WHERE ProductName = '16GB DDR4 RAM';

-- Check deletion log
SELECT 'Deletion Log After Delete:' AS note;
SELECT LogID, TableName, RecordID, DeletedByUserID, StoreID, DeletedAt, ExpiresAt, IsRestored 
FROM DELETION_LOG 
ORDER BY DeletedAt DESC 
LIMIT 5;

-- ======================
-- TEST 4: Restore from Soft-Delete
-- ======================
SELECT '=== TEST 4: Restore ===' AS test;

-- Restore the most recent deleted product (you may need to adjust RecordID based on output above)
-- Example (replace XXX with actual RecordID from deletion log if needed):
-- INSERT INTO PRODUCT (ProductID, StoreID, CategoryID, ProductName, Description, Price, Brand)
-- SELECT RecordID, StoreID, CategoryID, ProductName, Description, Price, Brand 
-- FROM (SELECT JSON_UNQUOTE(JSON_EXTRACT(SnapshotData, '$.ProductID')) AS RecordID, ... ) -- Complex, so manual for now

SELECT 'Manual Restore Note: Check DELETION_LOG and restore via JSON snapshot in code later' AS note;

-- ======================
-- TEST 5: Purge Event & Final Checks
-- ======================
SELECT '=== FINAL VERIFICATION ===' AS test;
SHOW TABLES;
SELECT VERSION() AS mysql_version;

SELECT 'Stock Health Summary:' AS summary;
SELECT HealthStatus, COUNT(*) AS count 
FROM STOCK 
GROUP BY HealthStatus;

SELECT 'Indexes on key tables:' AS indexes;
SHOW INDEX FROM PRODUCT;
SHOW INDEX FROM STOCK;
SHOW INDEX FROM DELETION_LOG;