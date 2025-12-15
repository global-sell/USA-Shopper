-- Add username column to users table
-- Run this SQL in your phpMyAdmin or MySQL client

ALTER TABLE `users` 
ADD COLUMN `username` VARCHAR(50) NULL UNIQUE AFTER `name`;

-- Update existing users with username based on email
UPDATE `users` 
SET `username` = SUBSTRING_INDEX(`email`, '@', 1) 
WHERE `username` IS NULL;

-- Make username NOT NULL after updating
ALTER TABLE `users` 
MODIFY COLUMN `username` VARCHAR(50) NOT NULL UNIQUE;
