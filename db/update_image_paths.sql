USE baddest_inventory;

UPDATE products SET image_path = 'assets/images/laptop.png' WHERE id = 1;
UPDATE products SET image_path = 'assets/images/mouse.png' WHERE id = 2;
UPDATE products SET image_path = 'assets/images/keyboard.png' WHERE id = 3;
UPDATE products SET image_path = 'assets/images/monitor.png' WHERE id = 4;
UPDATE products SET image_path = 'assets/images/headphones.jpg' WHERE id = 5;

-- Verify
SELECT id, name, image_path FROM products;