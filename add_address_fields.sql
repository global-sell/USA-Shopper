-- Add address fields to users table
-- Run this SQL in phpMyAdmin to add billing and shipping address fields

USE ybt_digital;

-- Add billing address fields
ALTER TABLE users 
ADD COLUMN billing_address TEXT NULL AFTER status,
ADD COLUMN billing_city VARCHAR(100) NULL AFTER billing_address,
ADD COLUMN billing_state VARCHAR(100) NULL AFTER billing_city,
ADD COLUMN billing_zip VARCHAR(20) NULL AFTER billing_state,
ADD COLUMN billing_country VARCHAR(100) NULL AFTER billing_zip,
ADD COLUMN billing_phone VARCHAR(20) NULL AFTER billing_country;

-- Add shipping address fields
ALTER TABLE users 
ADD COLUMN shipping_address TEXT NULL AFTER billing_phone,
ADD COLUMN shipping_city VARCHAR(100) NULL AFTER shipping_address,
ADD COLUMN shipping_state VARCHAR(100) NULL AFTER shipping_city,
ADD COLUMN shipping_zip VARCHAR(20) NULL AFTER shipping_state,
ADD COLUMN shipping_country VARCHAR(100) NULL AFTER shipping_zip,
ADD COLUMN shipping_phone VARCHAR(20) NULL AFTER shipping_country;

-- Add same_as_billing flag
ALTER TABLE users 
ADD COLUMN same_as_billing BOOLEAN DEFAULT TRUE AFTER shipping_phone;
