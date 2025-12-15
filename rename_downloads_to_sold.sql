-- Rename downloads column to sold in products table
-- Run this SQL in phpMyAdmin

USE ybt_digital;

-- Rename the column from downloads to sold
ALTER TABLE products 
CHANGE COLUMN downloads sold INT DEFAULT 0;
