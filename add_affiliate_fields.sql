-- Add affiliate/external product fields to products table
-- Run this SQL in phpMyAdmin

USE ybt_digital;

-- Add product type field
ALTER TABLE products 
ADD COLUMN product_type ENUM('simple', 'external', 'affiliate') DEFAULT 'simple' AFTER status;

-- Add external product URL
ALTER TABLE products 
ADD COLUMN external_url VARCHAR(500) NULL AFTER product_type;

-- Add affiliate link
ALTER TABLE products 
ADD COLUMN affiliate_link VARCHAR(500) NULL AFTER external_url;

-- Add button text for external products
ALTER TABLE products 
ADD COLUMN button_text VARCHAR(100) DEFAULT 'Buy Now' AFTER affiliate_link;

-- Add SKU field
ALTER TABLE products 
ADD COLUMN sku VARCHAR(100) NULL AFTER slug;

-- Add stock quantity
ALTER TABLE products 
ADD COLUMN stock_quantity INT DEFAULT 0 AFTER button_text;

-- Add regular price (for sale price comparison)
ALTER TABLE products 
ADD COLUMN regular_price DECIMAL(10, 2) NULL AFTER price;

-- Add sale price
ALTER TABLE products 
ADD COLUMN sale_price DECIMAL(10, 2) NULL AFTER regular_price;

-- Add featured flag
ALTER TABLE products 
ADD COLUMN is_featured BOOLEAN DEFAULT FALSE AFTER status;
