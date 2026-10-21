-- GamingPlace starter catalogue
-- These six entries use artwork already present in assets/images/.
-- Prices and descriptions are demo content for a portfolio store, not live offers.
-- Run this in phpMyAdmin after selecting the database used by GamingPlace.
-- Safe to run again: existing products with the same title are not re-inserted.
--
-- Expected products columns are the ones used by Product_model/views:
-- title, description, price, image. The id should auto-increment.

START TRANSACTION;

INSERT INTO products (title, description, price, image)
SELECT seed.title, seed.description, seed.price, seed.image
FROM (
    SELECT
        'Batman: Arkham Knight' AS title,
        'Take to the streets of Gotham in a cinematic action-adventure built around stealth, investigation, and the Dark Knight.' AS description,
        19.99 AS price,
        'BatmanArkhamKnight.jpg' AS image
    UNION ALL SELECT
        'Call of Duty: Ghosts',
        'A cinematic first-person shooter with squad-based missions, dramatic set pieces, and intense modern combat.',
        14.99,
        'CallofDutyGhosts.jpg'
    UNION ALL SELECT
        'Destiny',
        'Explore a science-fantasy universe, face formidable enemies, and gear up for your next interstellar adventure.',
        17.99,
        'Destiny.jpg'
    UNION ALL SELECT
        'Middle-earth: Shadow of Mordor',
        'Enter a dark fantasy world of exploration, combat, and a personal quest for vengeance across Mordor.',
        16.99,
        'ShadowofMordor.jpg'
    UNION ALL SELECT
        'Sniper Elite',
        'Plan your approach, use precision marksmanship, and work through tactical missions that reward patience.',
        12.99,
        'SniperElite.jpg'
    UNION ALL SELECT
        'Watch Dogs',
        'Navigate a connected city in an open-world action adventure where hacking and stealth change the way you play.',
        9.99,
        'WatchDogs.jpg'
) AS seed
WHERE NOT EXISTS (
    SELECT 1
    FROM products AS existing
    WHERE LOWER(TRIM(existing.title)) = LOWER(TRIM(seed.title))
);

COMMIT;

-- Verify the catalogue and image filenames:
SELECT id, title, price, image FROM products ORDER BY id;
