USE chimsiq_db;

-- Corrected & Optimized Indexes (Fixed for actual schema)

-- PRODUCT table (most important for searches)
-- CREATE INDEX idx_product_store_name ON PRODUCT (StoreID, ProductName);
-- CREATE INDEX idx_product_store_category ON PRODUCT (StoreID, CategoryID);
-- CREATE INDEX idx_product_store_brand ON PRODUCT (StoreID, Brand); 

-- 2. STOCK table (correct indexing via ProductID + health fields)
-- CREATE INDEX idx_stock_health ON STOCK (ProductID, HealthStatus, Quantity DESC);
-- CREATE INDEX idx_stock_minlevel ON STOCK (ProductID, MinStockLevel);

-- Other high-traffic tables
-- CREATE INDEX idx_po_store_status ON PURCHASE_ORDER (StoreID, Status, OrderDate DESC);
-- CREATE INDEX idx_supplier_store_name ON SUPPLIER (StoreID, SupplierName);
-- CREATE INDEX idx_category_store ON CATEGORY (StoreID, Status);

-- 4. Deletion Log & Backup (critical for maintenance)
-- CREATE INDEX idx_deletion_log_purge ON DELETION_LOG (StoreID, ExpiresAt, IsRestored);
-- CREATE INDEX idx_backup_store_created ON BACKUP_SNAPSHOT (StoreID, CreatedAt DESC);

-- Defaults & Modifications (safe)
ALTER TABLE STOCK 
    MODIFY Quantity INT NOT NULL DEFAULT 0,
    MODIFY MinStockLevel INT NOT NULL DEFAULT 5;

-- CHECK Constraint - Try without IF NOT EXISTS (will fail gracefully if already exists or version too old)
ALTER TABLE STOCK 
    ADD CONSTRAINT chk_minstock_positive CHECK (MinStockLevel >= 0);

-- Re-create Trigger (safe to re-run)
DROP TRIGGER IF EXISTS trg_stock_health_update;
DELIMITER //
CREATE TRIGGER trg_stock_health_update 
BEFORE UPDATE ON STOCK
FOR EACH ROW
BEGIN
    IF NEW.Quantity > NEW.MinStockLevel THEN
        SET NEW.HealthStatus = 'Healthy';
    ELSEIF NEW.Quantity > 0 THEN
        SET NEW.HealthStatus = 'Low';
    ELSE
        SET NEW.HealthStatus = 'Critical';
    END IF;
    SET NEW.LastUpdated = CURRENT_TIMESTAMP;
END //
DELIMITER ;

-- Re-create Purge Event
DROP EVENT IF EXISTS purge_expired_deletions;
DELIMITER //
CREATE EVENT purge_expired_deletions
ON SCHEDULE EVERY 1 DAY
DO
BEGIN
    UPDATE DELETION_LOG 
    SET IsRestored = 2, 
        SnapshotData = NULL 
    WHERE ExpiresAt < NOW() 
      AND IsRestored = 0;
END //
DELIMITER ;

-- VERIFICATION - Run these after the script
SHOW INDEX FROM PRODUCT;
SHOW INDEX FROM STOCK;
SHOW INDEX FROM PURCHASE_ORDER;
SHOW EVENTS LIKE 'purge_expired_deletions';
SELECT CONSTRAINT_NAME 
FROM information_schema.TABLE_CONSTRAINTS 
WHERE TABLE_SCHEMA = 'chimsiq_db' 
  AND TABLE_NAME = 'STOCK' 
  AND CONSTRAINT_TYPE = 'CHECK';