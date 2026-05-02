-- Add whatsapp column to products table
ALTER TABLE products
ADD COLUMN whatsapp VARCHAR(50) AFTER tokopedia_link;
