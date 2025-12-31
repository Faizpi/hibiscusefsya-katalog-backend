-- Add excerpt column to inspirations table
-- Run this in phpMyAdmin to add the new field

ALTER TABLE `inspirations` 
ADD COLUMN `excerpt` TEXT NULL AFTER `slug`;

-- Update existing records to have excerpt from first 150 chars of content
UPDATE `inspirations` 
SET `excerpt` = CONCAT(LEFT(`content`, 150), '...')
WHERE `excerpt` IS NULL OR `excerpt` = '';
