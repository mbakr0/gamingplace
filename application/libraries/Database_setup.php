<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Creates the minimum schema expected by GamingPlace when tables are missing.
 *
 * This is intentionally idempotent: existing tables and rows are not dropped.
 * The database itself must already exist and the configured DB user must have
 * CREATE and INSERT privileges.
 */
class Database_setup
{
    public function __construct()
    {
        $CI =& get_instance();

        if (!isset($CI->db) || !$CI->db->conn_id) {
            log_message('error', 'GamingPlace database setup skipped: database connection is unavailable.');
            return;
        }

        $statements = array(
            "CREATE TABLE IF NOT EXISTS `products` (
                `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `title` VARCHAR(255) NOT NULL,
                `description` TEXT NULL,
                `price` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
                `image` VARCHAR(255) NOT NULL DEFAULT '',
                PRIMARY KEY (`id`),
                UNIQUE KEY `uq_products_title` (`title`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
            "CREATE TABLE IF NOT EXISTS `users` (
                `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `first_name` VARCHAR(100) NOT NULL,
                `last_name` VARCHAR(100) NOT NULL,
                `email` VARCHAR(190) NOT NULL,
                `username` VARCHAR(50) NOT NULL,
                `password` VARCHAR(255) NOT NULL,
                `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                UNIQUE KEY `uq_users_email` (`email`),
                UNIQUE KEY `uq_users_username` (`username`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
            "CREATE TABLE IF NOT EXISTS `categoies` (
                `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `name` VARCHAR(100) NOT NULL,
                `description` TEXT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `uq_categoies_name` (`name`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
            "CREATE TABLE IF NOT EXISTS `orders` (
                `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                `product_id` INT UNSIGNED NOT NULL,
                `user_id` INT UNSIGNED NOT NULL,
                `transaction_id` VARCHAR(128) NOT NULL,
                `qty` INT UNSIGNED NOT NULL DEFAULT 1,
                `price` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
                `address` VARCHAR(255) NOT NULL,
                `address2` VARCHAR(255) NOT NULL DEFAULT '',
                `city` VARCHAR(100) NOT NULL,
                `state` VARCHAR(100) NOT NULL,
                `zipcode` VARCHAR(20) NOT NULL,
                `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                KEY `idx_orders_product_id` (`product_id`),
                KEY `idx_orders_user_id` (`user_id`),
                KEY `idx_orders_transaction_id` (`transaction_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
        );

        foreach ($statements as $sql) {
            if ($CI->db->query($sql) === FALSE) {
                log_message('error', 'GamingPlace database setup could not create a required table. Check TiDB permissions and schema compatibility.');
                return;
            }
        }

        $this->seedProducts($CI);
        $this->seedCategories($CI);
    }

    private function seedProducts($CI)
    {
        $products = array(
            array(
                'title' => 'Batman: Arkham Knight',
                'description' => 'Take to the streets of Gotham in a cinematic action-adventure built around stealth, investigation, and the Dark Knight.',
                'price' => 19.99,
                'image' => 'BatmanArkhamKnight.jpg'
            ),
            array(
                'title' => 'Call of Duty: Ghosts',
                'description' => 'A cinematic first-person shooter with squad-based missions, dramatic set pieces, and intense modern combat.',
                'price' => 14.99,
                'image' => 'CallofDutyGhosts.jpg'
            ),
            array(
                'title' => 'Destiny',
                'description' => 'Explore a science-fantasy universe, face formidable enemies, and gear up for your next interstellar adventure.',
                'price' => 17.99,
                'image' => 'Destiny.jpg'
            ),
            array(
                'title' => 'Middle-earth: Shadow of Mordor',
                'description' => 'Enter a dark fantasy world of exploration, combat, and a personal quest for vengeance across Mordor.',
                'price' => 16.99,
                'image' => 'ShadowofMordor.jpg'
            ),
            array(
                'title' => 'Sniper Elite',
                'description' => 'Plan your approach, use precision marksmanship, and work through tactical missions that reward patience.',
                'price' => 12.99,
                'image' => 'SniperElite.jpg'
            ),
            array(
                'title' => 'Watch Dogs',
                'description' => 'Navigate a connected city in an open-world action adventure where hacking and stealth change the way you play.',
                'price' => 9.99,
                'image' => 'WatchDogs.jpg'
            )
        );

        foreach ($products as $product) {
            $exists = $CI->db->where('title', $product['title'])->count_all_results('products');
            if ($exists === 0 && $CI->db->insert('products', $product) === FALSE) {
                log_message('error', 'GamingPlace could not seed starter product: ' . $product['title']);
            }
        }
    }

    private function seedCategories($CI)
    {
        $categories = array('Action', 'Adventure', 'Shooter');

        foreach ($categories as $name) {
            $exists = $CI->db->where('name', $name)->count_all_results('categoies');
            if ($exists === 0) {
                $CI->db->insert('categoies', array('name' => $name));
            }
        }
    }
}
