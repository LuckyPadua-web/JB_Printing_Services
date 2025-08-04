-- Add expected_delivery_date column to orders table
ALTER TABLE `orders` ADD COLUMN `expected_delivery_date` DATE DEFAULT NULL AFTER `placed_on`;