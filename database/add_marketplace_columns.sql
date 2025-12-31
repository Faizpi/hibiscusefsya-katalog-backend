-- Add shopee_link and tokopedia_link columns to products table
ALTER TABLE products 
ADD COLUMN shopee_link VARCHAR(500) AFTER image,
ADD COLUMN tokopedia_link VARCHAR(500) AFTER shopee_link;
