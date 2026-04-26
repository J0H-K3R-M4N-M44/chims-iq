USE chimsiq_db;

-- 1. Sample Store
INSERT IGNORE INTO STORE (StoreName) VALUES ('PC Hardware Hub');
SET @store_id = (SELECT StoreID FROM STORE WHERE StoreName = 'PC Hardware Hub' LIMIT 1);

-- 2. Superadmin (global)
INSERT IGNORE INTO USER (FullName, Email, PasswordHash, Role, StoreID) 
VALUES ('Super Admin', 'super@chims-iq.local', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'superadmin', NULL);

-- 3. Admin
INSERT IGNORE INTO USER (FullName, Email, PasswordHash, Role, StoreID) 
VALUES ('John Michael Aguelo', 'admin@pchardwarehub.ph', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', @store_id);
SET @admin_id = (SELECT UserID FROM USER WHERE Email = 'admin@pchardwarehub.ph' LIMIT 1);

-- Update store with admin
UPDATE STORE SET AdminUserID = @admin_id WHERE StoreID = @store_id;

-- 4. Sample Staff
INSERT IGNORE INTO USER (FullName, StaffID, PasswordHash, Role, StoreID) 
VALUES ('Kerbey Maaslom', 'TECH-001', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'staff', @store_id);

-- 5. Categories
INSERT IGNORE INTO CATEGORY (StoreID, CategoryName, Description, Status) VALUES 
(@store_id, 'GPU', 'Graphics Processing Units', 'active'),
(@store_id, 'RAM', 'Memory Modules', 'active'),
(@store_id, 'Storage', 'SSD & HDD', 'active');

-- 6. Suppliers
INSERT IGNORE INTO SUPPLIER (StoreID, SupplierName, Phone, Email, Address) VALUES 
(@store_id, 'JDM Components', '+63-917-123-4567', 'sales@jdm.ph', 'Manila'),
(@store_id, 'TechSupply PH', '+63-922-987-6543', 'orders@techsupply.ph', 'Quezon City');

-- 7. Sample Product + Stock (HealthStatus will be auto-set by trigger)
INSERT IGNORE INTO PRODUCT (StoreID, CategoryID, ProductName, Description, Price, Brand) 
VALUES (@store_id, 1, 'RTX 4060 8GB', 'NVIDIA Graphics Card', 18990.00, 'NVIDIA');

SET @prod_id = (SELECT ProductID FROM PRODUCT WHERE ProductName = 'RTX 4060 8GB' LIMIT 1);

INSERT IGNORE INTO STOCK (ProductID, Quantity, MinStockLevel) 
VALUES (@prod_id, 12, 5);   -- Should become 'Healthy'

-- Add one more for testing Low/Critical later
INSERT IGNORE INTO PRODUCT (StoreID, CategoryID, ProductName, Description, Price, Brand) 
VALUES (@store_id, 2, '16GB DDR4 RAM', 'Kingston Memory', 2499.00, 'Kingston');

SET @prod_id2 = (SELECT ProductID FROM PRODUCT WHERE ProductName = '16GB DDR4 RAM' LIMIT 1);
INSERT IGNORE INTO STOCK (ProductID, Quantity, MinStockLevel) 
VALUES (@prod_id2, 3, 8);   -- Should become 'Low'

-- Verification Queries - Run these and paste the output
SELECT '=== STORES ===' AS section;
SELECT StoreID, StoreName, AdminUserID FROM STORE;

SELECT '=== USERS ===' AS section;
SELECT UserID, FullName, Role, StoreID FROM USER;

SELECT '=== CATEGORIES ===' AS section;
SELECT * FROM CATEGORY;

SELECT '=== STOCK HEALTH TEST ===' AS section;
SELECT p.ProductName, s.Quantity, s.MinStockLevel, s.HealthStatus 
FROM PRODUCT p 
JOIN STOCK s ON p.ProductID = s.ProductID;

SELECT '=== INDEXES ON STOCK ===' AS section;
SHOW INDEX FROM STOCK;

SELECT '=== PURGE EVENT ===' AS section;
SHOW EVENTS LIKE 'purge_expired_deletions';