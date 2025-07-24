-- Add CVV column to existing payment_details table
-- Run this if you already have the database set up

USE chandrani_bookshop;

-- Add CVV column to payment_details table
ALTER TABLE payment_details ADD COLUMN cvv VARCHAR(4) DEFAULT NULL;

-- Update existing records with a default CVV (for demo purposes)
UPDATE payment_details SET cvv = '123' WHERE cvv IS NULL;

-- Make CVV NOT NULL after setting default values
ALTER TABLE payment_details MODIFY cvv VARCHAR(4) NOT NULL;
