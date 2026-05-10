-- ============================================================
-- inaffi.com — MySQL Database Schema
-- Version: 1.0.0 | Date: 2026-05-10
-- ============================================================
-- HOW TO RUN:
--   1. Login to Hostinger hPanel
--   2. Go to Databases → phpMyAdmin
--   3. Select your database from the left panel
--   4. Click the "SQL" tab at the top
--   5. Paste this entire file → Click "Execute"
-- ============================================================

SET NAMES utf8mb4;
SET time_zone = '+05:30';  -- India Standard Time
SET foreign_key_checks = 0;
SET sql_mode = 'STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- ============================================================
-- TABLE: creators
-- One row per registered influencer/creator account
-- ============================================================
CREATE TABLE IF NOT EXISTS creators (
    id               INT UNSIGNED     AUTO_INCREMENT PRIMARY KEY,
    username         VARCHAR(30)      NOT NULL UNIQUE,          -- public URL slug: inaffi.com/username
    display_name     VARCHAR(100)     NOT NULL,
    email            VARCHAR(255)     NOT NULL UNIQUE,
    password_hash    VARCHAR(255)     NOT NULL,                 -- bcrypt hash via password_hash()
    bio              VARCHAR(120)     DEFAULT NULL,             -- max 120 chars shown on storefront
    profile_image    VARCHAR(500)     DEFAULT NULL,             -- relative path: uploads/profiles/abc123.jpg
    instagram_handle VARCHAR(100)     DEFAULT NULL,             -- without @ symbol
    youtube_handle   VARCHAR(100)     DEFAULT NULL,
    facebook_handle  VARCHAR(100)     DEFAULT NULL,
    pinterest_handle VARCHAR(100)     DEFAULT NULL,
    is_admin         TINYINT(1)       NOT NULL DEFAULT 0,       -- 1 = access to /admin panel
    created_at       DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_username (username),
    INDEX idx_email    (email)

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================
-- TABLE: outfits
-- A "Look" — a curated collection of products by a creator
-- ============================================================
CREATE TABLE IF NOT EXISTS outfits (
    id           INT UNSIGNED     AUTO_INCREMENT PRIMARY KEY,
    creator_id   INT UNSIGNED     NOT NULL,
    title        VARCHAR(255)     NOT NULL,
    category     VARCHAR(50)      NOT NULL,                    -- Office, Casual, Festive, Beauty, Home, Other
    image        VARCHAR(500)     DEFAULT NULL,                -- relative path: uploads/outfits/abc123.jpg
    is_published TINYINT(1)       NOT NULL DEFAULT 0,          -- 0=draft (hidden), 1=live on storefront
    is_featured  TINYINT(1)       NOT NULL DEFAULT 0,          -- 1=shown on homepage (only one at a time)
    created_at   DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at   DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (creator_id) REFERENCES creators(id) ON DELETE CASCADE,

    INDEX idx_creator_published (creator_id, is_published),    -- storefront query
    INDEX idx_featured          (is_featured),                 -- homepage query
    INDEX idx_category          (category)                     -- filter query

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================
-- TABLE: products
-- Individual shoppable items within an outfit
-- ============================================================
CREATE TABLE IF NOT EXISTS products (
    id            INT UNSIGNED     AUTO_INCREMENT PRIMARY KEY,
    outfit_id     INT UNSIGNED     NOT NULL,
    name          VARCHAR(255)     NOT NULL,
    platform      VARCHAR(50)      NOT NULL,                   -- Amazon, Flipkart, Myntra, Nykaa, Ajio, Meesho, etc.
    affiliate_url TEXT             NOT NULL,                   -- full affiliate link pasted by creator
    price         VARCHAR(50)      DEFAULT NULL,               -- e.g. "₹1,499" — stored but NOT shown publicly
    image         VARCHAR(500)     DEFAULT NULL,               -- relative path: uploads/products/abc123.jpg (optional)
    display_order TINYINT UNSIGNED NOT NULL DEFAULT 0,         -- order within the outfit card
    in_stock      TINYINT(1)       NOT NULL DEFAULT 1,          -- 0=hidden from public storefront
    created_at    DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (outfit_id) REFERENCES outfits(id) ON DELETE CASCADE,

    INDEX idx_outfit    (outfit_id),                           -- fetch products for an outfit
    INDEX idx_in_stock  (outfit_id, in_stock),                 -- filter in-stock products
    INDEX idx_order     (outfit_id, display_order)             -- ordered product list

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================
-- TABLE: clicks
-- One row per affiliate link click — for creator stats
-- ============================================================
CREATE TABLE IF NOT EXISTS clicks (
    id         INT UNSIGNED     AUTO_INCREMENT PRIMARY KEY,
    product_id INT UNSIGNED     NOT NULL,
    outfit_id  INT UNSIGNED     NOT NULL,
    creator_id INT UNSIGNED     NOT NULL,
    clicked_at DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    user_agent VARCHAR(500)     DEFAULT NULL,
    referrer   VARCHAR(500)     DEFAULT NULL,

    -- No foreign keys on clicks — preserve history even if product/outfit is deleted
    INDEX idx_creator_time  (creator_id, clicked_at),         -- dashboard stats query
    INDEX idx_product       (product_id),                     -- product-level stats
    INDEX idx_outfit        (outfit_id),                      -- outfit-level stats
    INDEX idx_date          (clicked_at)                      -- date range queries

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================
-- TRIGGER: Enforce only ONE featured outfit at a time
-- When any outfit is set to is_featured = 1,
-- all other outfits are automatically set to is_featured = 0
-- ============================================================
DROP TRIGGER IF EXISTS trg_single_featured_outfit;

DELIMITER $$

CREATE TRIGGER trg_single_featured_outfit
BEFORE UPDATE ON outfits
FOR EACH ROW
BEGIN
    IF NEW.is_featured = 1 AND OLD.is_featured = 0 THEN
        UPDATE outfits
        SET    is_featured = 0
        WHERE  is_featured = 1
          AND  id != NEW.id;
    END IF;
END$$

DELIMITER ;


-- ============================================================
-- TRIGGER: Also handle INSERT with is_featured = 1
-- (in case a new outfit is inserted already featured)
-- ============================================================
DROP TRIGGER IF EXISTS trg_single_featured_outfit_insert;

DELIMITER $$

CREATE TRIGGER trg_single_featured_outfit_insert
BEFORE INSERT ON outfits
FOR EACH ROW
BEGIN
    IF NEW.is_featured = 1 THEN
        UPDATE outfits
        SET    is_featured = 0
        WHERE  is_featured = 1;
    END IF;
END$$

DELIMITER ;


-- ============================================================
-- VERIFY: Check tables were created correctly
-- Run this separately after the above to confirm setup
-- ============================================================
-- SHOW TABLES;
-- SHOW TRIGGERS;
-- DESCRIBE creators;
-- DESCRIBE outfits;
-- DESCRIBE products;
-- DESCRIBE clicks;


SET foreign_key_checks = 1;

-- ============================================================
-- SETUP COMPLETE
-- Next steps:
--   1. Upload all PHP files via FTP to public_html/
--   2. Fill in includes/config.php with your DB credentials
--   3. Create uploads/ folders in public_html/ (chmod 755)
--   4. Visit https://inaffi.com and sign up
--   5. Run: UPDATE creators SET is_admin = 1 WHERE username = 'your_username';
-- ============================================================
