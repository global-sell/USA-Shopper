-- Add icon/logo field to categories table
-- Run this SQL in phpMyAdmin

USE ybt_digital;

-- Add icon field for category logo/icon
ALTER TABLE categories 
ADD COLUMN icon VARCHAR(255) NULL AFTER description;
